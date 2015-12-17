<?php
namespace Tk;

use Tk\Form\Field;

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

    use \Tk\InstanceTrait;

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
     * @var Field\Event
     */
    protected $triggeredEvent = null;

    /**
     * @var array
     */
    protected $params = null;

    /**
     * @var array|\ArrayAccess
     */
    protected $request = null;


    /**
     * Create a form processor
     *
     * @param string $formId
     * @param array $params An array containing parameters that you may need for extending the form
     * @param array|\ArrayAccess $request
     */
    public function __construct($formId, $params = array(), $request = null)
    {
        $this->id = $formId;;
        $this->setInstanceId($formId);
        $this->name = $formId;
        $this->params = $params;
        if (!$request) {
            $request = $_REQUEST;
        }
        $this->request = $request;
        $this->setAttr('method', self::METHOD_POST);
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
     * Get a parameter from the array
     *
     * @param $name
     * @return bool
     */
    public function getParam($name)
    {
        if (!empty($this->params[$name])) {
            return $this->params[$name];
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function addParam($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * Get the param array
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return array|\ArrayAccess
     */
    public function &getRequest()
    {
        return $this->request;
    }

    /**
     * Execute the object
     *
     * If an button is found and its event is executed the result is returned
     *
     * @return mixed
     */
    public function execute()
    {
        if (!$this->isSubmitted()) return;

        $this->loadFromString($this->getRequest());

        /** @var Field\Event $event */
        $event = $this->getTriggeredEvent();
        if ($event) {
            if ($event->getCallback() instanceof \Closure || is_callable($event->getCallback())) {
                $ret = call_user_func_array($event->getCallback(), array($this));
                return $ret;
            }
        }
    }

    /**
     * Get the field event to execute
     *
     * This will only return a valid value <b>after</b> the
     *   execute() method has been called.
     *
     * @return Field\Event
     */
    public function getTriggeredEvent()
    {
        if ($this->request && !$this->triggeredEvent) {
            /* @var $field Field\Iface */
            foreach($this->fieldList as $field) {
                if ($field instanceof Field\Event) {
                    if (isset($this->request[$field->getName()])) {
                        $this->triggeredEvent = $field;
                    }
                }
            }
        }
        return $this->triggeredEvent;
    }

    /**
     * Set the callback to an event element,
     * The element must be of the type \Tk\Form\Field\Event
     *
     * @param string $name
     * @param callable $callback
     * @return Field\Event
     * @throws Exception
     */
    public function setEventCallback($name, $callback)
    {
        $field = $this->getField($name);
        if (!$field || !$field instanceof Field\Event) {
            throw new Exception('Event Field not found: `' . $name . '`');
        }
        $field->setCallback($callback);
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
     * Loads the form with the array.
     * This array must be in basic type form, for example
     * like the $_REQUEST, $_GET or $_POST array from a form.
     * EG:
     *   $array['field1'] = 'value1';
     *
     * @param array|\ArrayAccess $array
     * @return $this
     */
    public function loadFromString($array)
    {
        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $field) {
            $field->getType()->loadFromText($array);
        }
        return $this;
    }

    /**
     * Loads the form fields from the object using the fields complex type.
     *
     * This can be an array or object that contains the field's
     * values as their complex type, IE: \Tk\Date for a Date field
     * and so on...
     *
     * @param object $object The object being mapped.
     * @return $this
     */
    public function loadFromObject($object)
    {
        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $field) {
            $field->getType()->loadFromType((array)$object);
        }
        return $this;
    }


    /**
     * This will return an array of the field's
     * values as complex types,
     *
     * @return array
     */
    public function getValues()
    {
        $array = array();
        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $name => $field) {
            $array[$name] = $field->getValue();
        }
        return $array;
    }

    /**
     * Return an array of the fields raw string values
     *
     * @return array
     */
    public function getStringValues()
    {
        $array = array();
        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $name => $field) {
            $array = array_merge($array, $field->getType()->getTextValue());
        }
        return $array;
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
        $newArr = array();
        $newField->setForm($this);
        /* @var $field Field\Iface */
        foreach ($this->fieldList as $field) {
            if ($field->getName() == $fieldName) {
                $field->setForm($this);
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
        $newArr = array();
        $newField->setForm($this);
        /* @var $field Form\Element */
        foreach ($this->fieldList as $field) {
            $newArr[$field->getName()] = $field;
            if ($field->getName() == $fieldName) {
                $field->setForm($this);
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
     * @param string $name The element type name.
     * @return Field\Iface
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
     * Sets the value of an element type.
     *
     * @param string $name The field name.
     * @param mixed $value The field value.
     * @return Field\Iface
     * @throws Exception
     */
    public function setFieldValue($name, $value)
    {
        $field = $this->getField($name);
        if (!$field || !$field instanceof Field\Iface) {
            throw new Exception('Type not found: `' . $name . '`');
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
     * @param string $name A field name.
     * @param string $msg The error message.
     */
    public function addFieldError($name, $msg = '')
    {
        /* @var $field Field\Iface */
        $field = $this->getField($name);
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
        foreach ($errors as $name => $errorList) {
            $field = $this->getField($name);
            if (!$field) {
                $this->addError($errorList);
            } else {
                $field->addError($errorList);
            }
        }
    }

}
