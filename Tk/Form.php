<?php
namespace Tk;

use Tk\Form\Field;
use Tk\Form\Event;
use Tk\Form\FormEvents;


/**
 * The dynamic form processor
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
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Form extends Form\Element
{

    const ENCTYPE_URLENCODED        = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART         = 'multipart/form-data';
    const ENCTYPE_PLAIN             = 'text/plain';

    const METHOD_POST               = 'post';
    const METHOD_GET                = 'get';


    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var Field\Iface[]
     */
    protected $fieldList = array();

    /**
     * @var Event\Iface
     */
    protected $triggeredEvent = null;

    /**
     * @var array
     */
    protected $loadArray = null;

    /**
     * This will be set to true after the first call to load()
     * Allowing us to know when the fields have finished being added
     * to the form, good time to call init and trigger an event?
     * @var bool
     */
    protected $loading = false;

    /**
     * if true the required HTML5 attribute will be rendered
     * @var bool
     */
    private $enableRequiredAttr = false;

    /**
     * @var null|\Tk\Event\Dispatcher
     */
    protected $dispatcher = null;



    /**
     * Create a form processor
     *
     * @param string $formId
     * @param string $method
     * @param string|\Tk\Uri|null $action
     */
    public function __construct($formId, $method = self::METHOD_POST, $action = null)
    {
        $this->id = $formId;
        //$this->name = $formId;
        $this->setForm($this);
        $this->setAttr('method', $method);
        if (!$action)  $action = \Tk\Uri::create();
        $this->setAttr('action', \Tk\Uri::create($action));
    }

    /**
     * @param $formId
     * @param string $method
     * @param string|\Tk\Uri|null $action
     * @return static
     */
    public static function create($formId, $method = self::METHOD_POST, $action = null)
    {
        $obj = new static($formId, $method, $action);
        if (\Tk\Config::getInstance()->get('system.form.required.attr.enabled')) {
            $obj->setEnableRequiredAttr(true);
        }
        return $obj;
    }
    
    /**
     * Get the unique id for this element
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|\Tk\Event\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param null|\Tk\Event\Dispatcher $dispatcher
     */
    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Useful for extended form objects
     * To be called after all fields are added and
     */
    public function init()
    {
        if ($this->getDispatcher()) {
            $e = new \Tk\Event\FormEvent($this);
            $e->set('form', $this);
            $this->getDispatcher()->dispatch(FormEvents::FORM_INIT, $e);
        }
    }

    /**
     * Execute the object
     *
     * If an button is found and its event is executed the result is returned
     *
     * @param $request
     */
    public function execute($request = null)
    {
        if (!$request) {
            $request = \Tk\Request::create();
        }
        // Load default field values
        $this->loadFields($this->loadArray);
        if ($this->getDispatcher()) {
            $e = new \Tk\Event\FormEvent($this);
            $e->set('form', $this);
            $this->getDispatcher()->dispatch(FormEvents::FORM_LOAD, $e);
        }

        // get the triggered event, this also setup the form ready to fire an event if present.
        /* @var Event\Iface|null $event */
        $event = $this->getTriggeredEvent($request);
        if (!$this->isSubmitted()) return;

        // Load request field values
        $cleanRequest = $this->cleanLoadArray($request);
        $this->loadFields($cleanRequest);

        if ($this->getDispatcher()) {
            $e = new \Tk\Event\FormEvent($this);
            $e->set('form', $this);
            $this->getDispatcher()->dispatch(FormEvents::FORM_SUBMIT, $e);
        }

        if ($event) {
            $event->execute();
        }
    }

    /**
     * Loads the fields with values from an array.
     * EG:
     *   $array['field1'] = 'value1';
     *
     * @param array|\ArrayObject $array
     * @return $this
     */
    protected function loadFields($array)
    {
        if ($array === null) return $this;

        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $field) {
            if ($field instanceof Event\Iface) continue;
            $field->load($array);
        }
        return $this;
    }

    /**
     * Loads the fields with values from an array.
     * EG:
     *   $array['field1'] = 'value1';
     *
     * @param array $array
     * @return $this
     * @throws Exception
     */
    public function load($array = array())
    {
        if (!$this->loading) {
            $this->init();
            $this->loading = true;
        }
        if ($this->loadArray === null) $this->loadArray = array();
        if (is_array($array)) {
            $this->loadArray = array_merge($this->loadArray, $array);
        }
        return $this;
    }
    
    /**
     * Clean the load() array
     *  o create a new raw array for any \ArrayAccess objects like the request object
     *  o add array keys that the request modifies (request replaces '.' with '_') with field names
     *    this will not modify keys that a field does not exist for.
     * 
     * @param array|\ArrayAccess $array
     * @return array
     */
    protected function cleanLoadArray($array)
    {
        // get values from \ArrayAccess objects (IE: Request object)
        if ($array instanceof \ArrayAccess) {
            $a = array();
            foreach($array as $k => $v) $a[$k] = $v;
            $array = $a;
        }

        // Fix keys for conversions of '_' to '.' for fields that have been modified
        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $field) {
            $cleanName = str_replace('.', '_', $field->getName());
            if (array_key_exists($cleanName, $array) && !array_key_exists($field->getName(), $array)) {
                $array[$field->getName()] = $array[$cleanName];
            }
        }

        return $array;
    }

    /**
     * Get the field event to execute
     *
     * This will only return a valid value <b>after</b> the
     *   execute() method has been called.
     *
     * @param array $array
     * @return Event\Iface
     */
    public function getTriggeredEvent($array = null)
    {
        if ($array && !$this->triggeredEvent) {
            /* @var $field Field\Iface */
            foreach($this->fieldList as $field) {
                if ($field instanceof Event\Iface) {
                    if (isset($array[$field->getEventName()])) {
                        $this->triggeredEvent = $field;
                        break;
                    }
                }
            }
        }
        return $this->triggeredEvent;
    }


    /**
     * Add a callback to an event element,
     * The element must be of the type \Tk\Form\Field\Event
     *
     * @param string $fieldName
     * @param callable $callback
     * @return Event\Iface
     * @throws Form\Exception
     */
    public function addEventCallback($fieldName, $callback)
    {
        $fieldName = str_replace('[]', '', $fieldName);
        $field = $this->getField($fieldName);
        if (!$field || !$field instanceof Event\Iface) {
            throw new Form\Exception('Event Field not found: `' . $fieldName . '`');
        }
        $field->addCallback($callback);
        return $field;
    }

    /**
     * Check if the form has been submitted
     *
     * @return bool
     */
    public function isSubmitted()
    {
        if ($this->getTriggeredEvent()) {
            return true;
        }
        return false;
    }

    /**
     * Add an field to this form
     *
     * @param Field\Iface $field
     * @return Field\Iface
     */
    public function addField($field)
    {
        $this->fieldList[$field->getName()] = $field;
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
    public function addFieldBefore($fieldName, $newField)
    {
        $fieldName = str_replace('[]', '', $fieldName);
        $newArr = array();
        $newField->setForm($this);
        /** @var Field\Iface $field */
        foreach ($this->fieldList as $field) {
            if ($field->getName() == $fieldName) {
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
    public function addFieldAfter($fieldName, $newField)
    {
        $fieldName = str_replace('[]', '', $fieldName);
        $newArr = array();
        $newField->setForm($this);
        /** @var Field\Iface $field */
        foreach ($this->fieldList as $field) {
            $newArr[$field->getName()] = $field;
            if ($field->getName() == $fieldName) {
                $newArr[$newField->getName()] = $newField;
            }
        }
        $this->fieldList = $newArr;
        return $newField;
    }

    /**
     * Remove a field from the form
     *
     * @param string $fieldName
     * @return boolean
     */
    public function removeField($fieldName)
    {
        $fieldName = str_replace('[]', '', $fieldName);
        if (isset($this->fieldList[$fieldName])) {
            unset($this->fieldList[$fieldName]);
            return true;
        }
        return false;
    }

    /**
     * Return a field object or null if not found
     *
     * @param string $fieldName
     * @return Field\Iface|null
     */
    public function getField($fieldName)
    {
        $fieldName = str_replace('[]', '', $fieldName);
        if (array_key_exists($fieldName, $this->fieldList)) {
            return $this->fieldList[$fieldName];
        }
        return null;
    }

    /**
     * Set the field array
     *
     * @param array $arr
     * @return $this
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
     * @param string $fieldName The element type name.
     * @return string|array
     */
    public function getFieldValue($fieldName)
    {
        $fieldName = str_replace('[]', '', $fieldName);
        $field = $this->getField($fieldName);
        if ($field instanceof Field\Iface) {
            return $field->getValue();
        }
        return null;
    }

    /**
     * Sets the value of an element type.
     *
     * @param string $fieldName The field name.
     * @param mixed $value The field value.
     * @return Field\Iface
     * @throws Exception
     */
    public function setFieldValue($fieldName, $value)
    {
        $fieldName = str_replace('[]', '', $fieldName);
        $field = $this->getField($fieldName);
        if (!$field || !$field instanceof Field\Iface) {
            throw new Exception('Type not found: `' . $fieldName . '`');
        }
        $field->setValue($value);
        return $field;
    }

    /**
     * Does this form contain errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        /* @var $field Field\Iface */
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
     * Get all the errors associated with this forms request
     *
     * @return array
     */
    public function getAllErrors()
    {
        $e = $this->errors;
        /* @var $field Field\Iface */
        foreach($this->getFieldList() as $field) {
            if ($field->hasErrors()) {
                $e[$field->getName()] = array_merge($e, $field->getErrors());
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
     * @param string $fieldName A field name.
     * @param string $msg The error message.
     */
    public function addFieldError($fieldName, $msg = '')
    {
        $fieldName = str_replace('[]', '', $fieldName);
        /* @var $field Field\Iface */
        $field = $this->getField($fieldName);
        if ($field) {
            $field->addError($msg);
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
        foreach ($errors as $fieldName => $errorList) {
            $fieldName = str_replace('[]', '', $fieldName);
            $field = $this->getField($fieldName);
            if (!$field) {
                $this->addError($errorList);
            } else {
                $field->addError($errorList);
            }
        }
    }

    /**
     * This will return an array of the field's values,
     *
     * @param null|array|string $regex A regular expression or array of field names to get
     * @return array
     */
    public function getValues($regex = null)
    {
        $array = array();
        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $name => $field) {
            if ($field instanceof Event\Iface) continue;
            if ($regex) {
                if (is_string($regex) && !preg_match($regex, $name)) {
                    continue;
                } else if (is_array($regex) && !in_array($name, $regex)) {
                    continue;
                }
            }
            $value = $field->getValue();
            if (!$field->isArrayField() && is_array($value)) {
                // Keep an eye on this for any unwanted issues, related to the data map and multiple value property maps
                foreach ($value as $k => $v) {  // pull values out if the element is not an array
                    $array[$k] = $v;
                }
            } else {
                $array[$name] = $value;
            }
        }
        return $array;
    }

    /**
     * @return bool
     */
    public function isEnableRequiredAttr()
    {
        return $this->enableRequiredAttr;
    }

    /**
     * @param bool $enableRequiredAttr
     */
    public function setEnableRequiredAttr($enableRequiredAttr)
    {
        $this->enableRequiredAttr = $enableRequiredAttr;
    }
    
    /**
     * Not used in the form
     *
     * @return void|string|\Dom\Template
     */
    public function show() {}
    
    
}
