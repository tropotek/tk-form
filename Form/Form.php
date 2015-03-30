<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace  Form;

/**
 * The dynamic form Controller
 *
 * `enctype` Attribute Values:
 * <code>
 *              Value                    |                 Description
 * --------------------------------------|---------------------------------------
 *  application/x-www-form-urlencoded    |  All characters are encoded before sent (this is default)
 *  multipart/form-data                  |  No characters are encoded. This value is required when you are using forms that have a file upload control
 *  text/plain                           |  Spaces are converted to "+" symbols, but no special characters are encoded
 * </code>
 *
 *
 * accept-charset is set as the $encoding parameter or use setEncoding()
 *
 *
 * @package \Form
 */
class Form extends Element
{


    const ENCTYPE_URLENCODED        = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART         = 'multipart/form-data';
    const ENCTYPE_PLAIN             = 'text/plain';

    const METHOD_POST               = 'post';
    const METHOD_GET                = 'get';

    const HIDDEN_SUBMIT_ID          = '__submitId';


    const EVENT_PRE_INIT            = '__preInit__';
    const EVENT_POST_INIT           = '__postInit__';

    const EVENT_PRE_EXE             = '__preExecute__';
    const EVENT_POST_EXE            = '__postExecute__';

    const EVENT_PRE_SUBMIT          = '__preSubmit__';
    const EVENT_POST_SUBMIT         = '__postPostSubmit__';

    const EVENT_PRE_LOAD_REQUEST    = '__preLoadRequest__';



    /**
     * A unique form number
     * @var int
     */
    protected $idx = 0;

    /**
     * @var \Tk\Url
     */
    protected $action = null;

    /**
     * @var string
     */
    protected $method = self::METHOD_POST;

    /**
     * @var string
     */
    protected $encoding = '';

    /**
     * @var string
     */
    protected $enctype = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $target = '';

    /**
     * @var array
     */
    protected $helpList = array();

    /**
     * @var array
     */
    protected $fieldList = array();

    /**
     * The executed event name
     * NOTE: This is only set after an event controller is executed.
     * @var string
     */
    protected $executedEvent = null;

    /**
     * @var array
     */
    protected $eventList = array();

    /**
     * @var bool
     */
    protected $executed = false;

    /**
     * Could be set after the events have been executed
     * @var \Tk\Url
     */
    protected $redirectUrl = null;

    /**
     * This should be an array or an object with that holds the form data
     * @var mixed
     */
    protected $object = null;

    /**
     * A copy of the original object
     * @var mixed
     */
    protected $objectOrg = null;






    /**
     * Create a form controller
     * NOTE: Use the function Form::create() in most comment uses
     *
     * @param string $formId
     * @param mixed $object
     */
    public function __construct($formId, $object = null)
    {
        static $idx = 0;
        $this->idx = $idx++;
        $this->object = $object;
        $this->name = $formId;
        $this->form = $this;
        $this->setAction($this->getUri());
    }

    /**
     *
     * @param type $list
     * @return array
     */
    static public function selectInterface2Array($list)
    {
        $arr = array();
        /* @var $obj SelectInterface */
        foreach ($list as $k => $obj) {
            $arr[$obj->getSelectValue()] = $obj->getSelectText();
        }
        return $arr;
    }


    /**
     * Test that the form has been submitted and
     * the correct hidden data is available
     *
     * @return bool
     */
    public function isSubmitted()
    {
        if (trim($this->getRequest()->get(self::HIDDEN_SUBMIT_ID)) == $this->form->getId()) {
            return true;
        }
        return false;
    }



    /**
     * Init the object
     */
    public function init()
    {
        tklog('Form::init("'.$this->getId().'")');
        
        // The following link has a good idea for bots, also we would need a method to accept external forms.
        // Maybe external forms can contain a standard hidden field and internal forms can have field with
        // a random name that should be empty.... Considder...
        // http://feedproxy.google.com/~r/SitepointFeed/~3/XsAe770G6y8/
        $this->addField(new Field\Hidden(self::HIDDEN_SUBMIT_ID))->setValue($this->getId());


        $this->notify(self::EVENT_PRE_INIT);
        if (is_array($this->object)) {
            $this->loadFromArray($this->object);
        } else if ($this->object instanceof \Tk\Model\Model) {
            $this->loadFromObject($this->object);
        }
        if (!$this->isSubmitted()) {
            return;
        }

        $this->notify(self::EVENT_PRE_LOAD_REQUEST);
        $this->form->loadFromArray();
        $this->notify(self::EVENT_POST_INIT);

    }



    /**
     * Execute the object
     */
    public function execute()
    {
        if (!$this->isSubmitted() || $this->executed) {
            return;
        }
        tklog('Form::execute("'.$this->getId().'")');
        
        $this->executedEvent = $this->findExecutedEvent();
        $this->executed = true;
        $this->notify(self::EVENT_PRE_EXE);
        if ($this->executedEvent) {
            $this->notify(self::EVENT_PRE_SUBMIT);
            $this->notify($this->executedEvent);
            $this->notify(self::EVENT_POST_SUBMIT);
        }
        $this->notify();
        $this->notify(self::EVENT_POST_EXE);


        if ($this->hasErrors()) {
            $err = $this->getErrors(true);
            \Tk\Log\Log::write('Form/Fields Have Errors - Form Errors: ' . print_r($err,true));
        } else {
            $url = null;
            if($this->redirectUrl) {
                $url = \Tk\Url::create($this->redirectUrl);
            }
            $evt = @$this->eventList[$this->executedEvent];
            if ($evt && $evt->getRedirectUrl()) {
                $url = $evt->getRedirectUrl();
            }

            if ($url) {
                if ($this->getObject() instanceof \Tk\Db\Model) {
                    if ($evt->getName() == 'save' || $evt->getName() == 'create') {
                        $arr = explode('\\', get_class($this->getObject()));
                        $idVar = lcFirst(array_pop($arr)) . 'Id';
                        $url->set($idVar, $this->getObject()->getVolitileId());
                    }
                }
                \Tk\Url::create($url)->redirect();
            }
        }
    }

    /**
     * makeFieldId
     *
     * @param string $fieldName
     * @return string
     */
    public function makeFieldId($fieldName = '')
    {
        return $this->getForm()->getId() . '_' . $fieldName;
    }

    /**
     * This function searched the request and the event list looking for the correct event
     * name to execute.
     *
     * By default it will trigger the first buttonEvent where it finds its name in the request parameter list.
     *
     * @return string
     */
    private function findExecutedEvent()
    {
        foreach ($this->form->getObservable()->getObserverNames() as $name) {
            if (!$name) { continue; }
            if ( in_array($name, array_keys(\Tk\Request::getInstance()->getAll())) ) {
                return $name;
            }
        }
    }

    /**
     * Get the executed event
     *
     * NOTE: This is only set after an event controller is executed.
     * @return string
     */
    public function getExecutedEvent()
    {
        return $this->executedEvent;
    }

    /**
     * Get the object/array that we will be acting on during this event.
     *
     * @return Object or an array()
     */
    public function getObject()
    {
        if ($this->object) {
            return $this->object;
        }
        return $this->getValuesArray();
    }


    /**
     * If a redirect url is set then the form is redirected to there
     *
     * @param \Tk\Url $url
     * @return Form
     */
    public function setRedirectUrl($url = null)
    {
        $this->redirectUrl = $url;
        return $this;
    }

    /**
     * Get the redirect url if available
     *
     * @return \Tk\Url
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * An alias for attach(), Adds the form to the
     *
     * @param \Form\Event\Iface|\Tk\Observer $obs
     * @param string $name
     * @param int $idx
     * @return \Tk\Observer
     */
    public function attach(\Tk\Observer $obs, $name = '', $idx = null)
    {
        $obs->setForm($this);
        if (!$name && !$obs instanceof Event\Hidden && method_exists($obs, 'getName')) {
            $name = $obs->getName();
        }
        $this->eventList[$name] = $obs;
        return parent::attach($obs, $name, $idx);
    }



    /**
     * Get the form name/ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->getName();
    }

    /**
     * Get this form's rendered title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the form rendered title
     *
     * @param string $str
     * @return Form
     */
    public function setTitle($str)
    {
        $this->title = $str;
        return $this;
    }

    /**
     * Get this form's rendered target attribute
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set the form rendered target attribute
     * Default: null
     *
     * @param string $str
     * @return Form
     */
    public function setTarget($str)
    {
        $this->target = $str;
        return $this;
    }

    /**
     * Set the form submit action
     *
     * @param \Tk\Url $url
     * @return Form
     */
    public function setAction($url)
    {
        $this->action = $url;
        return $this;
    }

    /**
     * Get the action url
     *
     * @return \Tk\Url
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set the form encoding type
     * This should be set to ENCTYPE_MULTIPART for forms that submit files
     *
     * @param string $enctype
     * @return Form
     */
    public function setEnctype($enctype)
    {
        $this->enctype = strtolower($enctype);
        return $this;
    }

    /**
     * Get the form encoding type
     *
     * @return string
     */
    public function getEnctype()
    {
    	return $this->enctype;
    }

    /**
     * Set the characterset encoding Defaults to UTF-8
     *
     * @param string $encoding
     * @return Form
     */
    public function setEncoding($encoding)
    {
        $this->encoding = strtolower($encoding);
        return $this;
    }

    /**
     * Get the form encoding type
     *
     * @default utf-8
     * @return string
     */
    public function getEncoding()
    {
    	return $this->encoding;
    }

    /**
     * Set this forms method, default post
     *
     * @param string $method
     * @return Form
     */
    public function setMethod($method)
    {
        if ($method) {
            $this->method = strtolower($method);
        }
        return $this;
    }

    /**
     * Get the form method 'GET, POST'
     *
     * @return string
     */
    public function getMethod()
    {
    	return $this->method;
    }




    /**
     * Add a help message to the form
     *
     * @param string $title
     * @param string $msg
     */
    public function addHelpMessage($title, $msg)
    {
        //$msg = strip_tags($msg, '<a>,<b>,<strong>,<u>,<i>,<em>,<img>');
        $this->helpList[$title] = $msg;
    }

    /**
     * Clear the help message List
     */
    public function clearHelpList()
    {
        $this->helpList = array();
    }

    /**
     * Get the help message list.
     * This text is rendered around the form in a position helpful to the user.
     *
     * @return array
     */
    public function getHelpList()
    {
    	return $this->helpList;
    }



    /**
     * Add an element to this element
     *
     * @param Field\Iface $field
     * @return Field\Iface
     */
    public function addField($field)
    {
        $this->fieldList[$field->getName()] = $field;
        $field->setInstanceId($this->getInstanceId());
        $field->setForm($this);
        return $field;
    }

    /**
     * Add a field element before another element
     *
     * @param string $fieldName
     * @param Field\Iface $newField
     * @return Field\Iface
     */
    public function addBefore($fieldName, $newField)
    {
        $newArr = array();
        $newField->setInstanceId($this->getInstanceId());
        $newField->setForm($this);
        /* @var $field Field\Iface */
        foreach ($this->fieldList as $field) {
            if ($field->getName() == $fieldName) {
                $field->setForm($this->getForm());
                $newArr[$newField->getName()] = $newField;
            }
            $newArr[$field->getName()] = $field;
        }
        $this->fieldList = $newArr;
        return $newField;
    }

    /**
     * Add an element after another element
     *
     * @param string $fieldName
     * @param Field\Iface $newField
     * @return Field\Iface
     */
    public function addAfter($fieldName, $newField)
    {
        $newArr = array();
        $newField->setInstanceId($this->getInstanceId());
        $newField->setForm($this);
        /* @var $field Field\Iface */
        foreach ($this->fieldList as $field) {
            $newArr[$field->getName()] = $field;
            if ($field->getName() == $fieldName) {
                $field->setForm($this->getForm());
                $newArr[$newField->getName()] = $newField;
            }
        }
        $this->fieldList = $newArr;
        return $newField;
    }

    /**
     * Remove a field from the form
     *
     * @param $fieldName
     * @return boolean
     *
     * @added Ver \Form 1.1.5
     * @experimental
     */
    public function removeField($fieldName)
    {
        if (isset($this->fieldList[$fieldName])) {
            unset($this->fieldList[$fieldName]);
            return true;
        }
        return false;
    }

    /**
     * Return a field object or null if not found
     *
     * @param string $name
     * @return Field\Iface
     */
    public function getField($name)
    {
        if (array_key_exists($name, $this->fieldList)) {
            return $this->fieldList[$name];
        }
    }

    /**
     * Set the field array
     *
     * @param array $arr
     * @return Field\Iface
     */
    public function setFieldList($arr = array())
    {
        $this->fieldList = $arr;
        return $this;
    }

    /**
     * Get the field array
     *
     * @return array
     */
    public function getFieldList()
    {
        return $this->fieldList;
    }


    /**
     * Returns a form field value. Returns NULL if no field exists
     *
     * @param string $name The field name.
     * @return mixed
     */
    public function getFieldValue($name)
    {
        $field = $this->getField($name);
        if ($field instanceof Field\Iface) {
            return $field->getValue();
        }
        return null;
    }

    /**
     * Sets the value of a form field.
     *
     * @param string $name The field name.
     * @param mixed $value The field value.
     */
    public function setFieldValue($name, $value)
    {
        $field = $this->getField($name);
        if (!$field || !$field instanceof Field\Iface) {
            tklog('Field not found: `' . $name . '`', \Tk\Log\Log::NOTICE);
            return;
        }
        $field->setValue($value);
    }

    /**
     * Returns true if the fields in a tabgroup has errors
     *
     * @param $group
     * @return bool
     */
    public function groupHasErrors($group)
    {
        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $field) {
            if ($field->getTabGroup() == $group && $field->hasErrors()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Does this element contain errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        foreach ($this->fieldList as $field) {
            if ($field->hasErrors()) {
                return true;
            }
        }
        if (count($this->getErrors())) {
            return true;
        }
        return false;
    }


    /**
     *
     * @return array
     */
    public function getErrors($withFields = false)
    {
        $e = parent::getErrors();
        if ($withFields) {
            /* @var $field \Form\Field\Iface */
            foreach($this->getFieldList() as $field) {
                if ($field->hasErrors()) {
                    $e[$field->getName()] = array_merge($e, $field->getErrors());
                }
            }
        }
        return $e;
    }



    /**
     * Adds field error.
     *
     * If the field is not found in the form then the error message is set to
     * the form error message.
     *
     * If $msg is null the field's error list is cleared
     *
     * @param string $name A field name.
     * @param string $msg The error message.
     */
    public function addFieldError($name, $msg)
    {
        /* @var $field Field\Iface */
        $field = $this->getField($name);
        if ($field) {
            $field->addError($msg);
            if (!$msg) {
                $field->clearErrorList();
            }
        } else {
            $this->addError($msg);
        }
    }

    /**
     * Adds form field errors from a map of (field name, list of errors) message pairs.
     *
     * If the field is not found in the form then the error message is added to
     * the form error messages.
     *
     * @param array $errors
     */
    public function addFieldErrors($errors)
    {
        foreach ($errors as $name => $errorList) {
            $field = $this->getField($name);
            if (!$field) {
                $this->addError($errorList);
            } else {
                $field->addError($errorList);
            }
        }
    }

    /**
     * Test to see if the fields in this form use groups
     *
     * @return bool
     */
    public function hasTabGroups()
    {
        foreach ($this->fieldList as $field) {
            if ($field->getTabGroup()) {
                return true;
            }
        }
        return false;
    }







    /**
     * Loads the form with the array.
     * This array must be in basic type form, for example
     * like the $_REQUEST, $_GET or $_POST array from a form.
     * EG:
     *   $array['field1'] = 'value1';
     *
     * @param array $array If null the $_REQUEST array is used...
     * @return Form
     */
    public function loadFromArray($array = null)
    {
        if (!$array) {
            $array = $this->getRequest()->getAll();
        }
        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $field) {
            //if (!$field->isLoadable())  continue;
            $field->getType()->loadFromFormArray($array);
        }
        return $this;
    }

    /**
     * Loads the form fields from the object.
     * This can be an array or object that contains the field's
     * values as their complex type, IE: \Tk\Date for a Date field
     * and so on...
     *
     * @param array|stdClass $object The object being mapped.
     * @return Form
     */
    public function loadFromObject($object)
    {
        if (!$object) return;
        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $field) {
            //if (!$field->isLoadable()) continue;
            $field->getType()->loadFromObjectArray((array)$object);
        }
        return $this;
    }

    /**
     * Loads an object with the contents of the form field
     * complex types. This method only inserts the fields that
     * have the same name as a the objects property.
     *
     * @param mixed $object
     * @throws Exception
     * @return Form
     */
    public function loadObject($object)
    {
    	if (!is_object($object)) {
    		throw new Exception('Invalid object type for parameter.');
    	}

        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $name => $field) {
            if ($field->isReadonly() || !$name) continue;
            //if ($field->isReadonly() || !$name || preg_match('/^_/', $name)) continue;
            $val = $field->getValue();
            if (!$field->isMultiField()) {
                if (property_exists($object, $name)) {
                    $object->$name = $val;
                }
            } else {
                foreach ($val as $n => $v) {
                    if (property_exists($object, $n)) {
                        $object->$n = $v;
                    }
                }
            }
        }
        return $object;
    }


    /**
     * Return an array of the fields complex type values
     *
     * @return array
     */
    public function getValuesArray()
    {
        $array = array();
        /* @var $field \Form\Field\Iface */
        foreach ($this->getFieldList() as $name => $field) {
            if ($field->isReadonly() || !$name) continue;

            // TODO: Keep an eye on this monster?????
            $val = $field->getType()->getFieldValues();
            //$val = $field->getValue();

            if (!$field->isMultiField()) {
                $array[$name] = $val;
            } else {
                foreach ($val as $n => $v) {
                    $array[$n] = $v;
                }
            }
        }
    	return $array;
    }


    /**
     * This will return an array of the field's
     * values as native types, ie as if they were
     * submitted from a form.
     *
     * @return array
     */
    public function getFormValuesArray()
    {
        $array = array();
        foreach ($this->getFieldList() as $name => $field) {
            if ($field->isReadonly() || !$name) continue;
            $array = array_merge($array, $field->getType()->getFieldValues());
        }
    	return $array;
    }

}