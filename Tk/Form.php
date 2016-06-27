<?php
namespace Tk;

use Tk\Form\Exception;
use Tk\Form\Field;
use Tk\Form\Event;


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
     * @var array|\ArrayAccess
     */
    protected $request = null;


    /**
     * Create a form processor
     *
     * @param string $formId
     * @param array $request
     */
    public function __construct($formId, $request = null)
    {
        $this->id = $formId;;
        $this->setForm($this);
        $this->name = $formId;
        if (!$request) {
            $request = $_REQUEST;
        }
        $this->request = $request;
        $this->setAttr('method', self::METHOD_POST);
        $this->setAttr('action', \Tk\Uri::create());
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
     * @return array
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
        $this->load($this->getRequest());
        /** @var Event\Iface $event */
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
     * @return Event\Iface
     */
    public function getTriggeredEvent()
    {
        if ($this->request && !$this->triggeredEvent) {
            /* @var $field Field\Iface */
            foreach($this->fieldList as $field) {
                if ($field instanceof Event\Iface) {
                    if (isset($this->request[$field->getName()])) {
                        $this->triggeredEvent = $field;
                        break;
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
     * @return Event\Iface
     * @throws Exception
     */
    public function setEventCallback($name, $callback)
    {
        $field = $this->getField($name);
        if (!$field || !$field instanceof Event\Iface) {
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
        /* @var $field Field\Iface */
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
     * @return Field\Iface|null
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
     * @return string|array
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
    
    /**
     * Loads the fields with values from an array.
     * EG:
     *   $array['field1'] = 'value1';
     *
     * @param array|\ArrayAccess $array
     * @return $this
     * @throws Exception
     */
    public function load($array)
    {
        $array = $this->cleanLoadArray($array);
        if (!is_array($array)) {
            throw new Exception('Convert any loadable data to an array first.');
        }
        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $field) {
            if ($field instanceof Event\Iface) continue;
            $field->setValue($array);
        }
        return $this;
    }

    /**
     * Clean the load() array
     *  o create a new raw array for any \ArrayAccess objects
     *  o add array keys that request modifies (ie replace '_' with '.') with field names
     *    this will not modify keys that a field does not exist for.
     * 
     * @param array|\ArrayAccess $array
     * @return array
     */
    protected function cleanLoadArray($array)
    {
        // get values from \ArrayAccess objects
        if ($array instanceof \ArrayAccess) {
            $a = [];
            foreach($array as $k => $v) $a[$k] = $v;
            $array = $a;
        }
        // Fix keys for conversions of '.' to '_'
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
     * This will return an array of the field's values,
     *
     * @return array
     */
    public function getValues()
    {
        $array = array();
        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $name => $field) {
            if ($field instanceof Event\Iface) continue;
            $array[$name] = $field->getValue();
        }
        return $array;
    }

    /**
     * Not used in the form
     *
     * @return string|\Dom\Template
     */
    public function getHtml() {}
    
    
}