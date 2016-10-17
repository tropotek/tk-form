<?php
namespace Tk;

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
     * @var array
     */
    protected $loadArray = null;


    /**
     * Create a form processor
     *
     * @param string $formId
     * @param array $request An array of request GET|POST values
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
        $this->executeLoad($this->loadArray, true);

        if (!$this->isSubmitted()) return null;
        $this->executeLoad($this->getRequest());

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
     * Loads the fields with values from an array.
     * EG:
     *   $array['field1'] = 'value1';
     *
     * @param array $array
     * @param bool $ignoreHidden If this is true then if the field does not exist in the array the setValue() is not executed (Good for checkboxes and radios)
     * @return $this
     */
    protected function executeLoad($array, $ignoreHidden = false)
    {
        if ($array === null) return $this;
        $array = $this->cleanLoadArray($array);

        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $field) {
            if ($field instanceof Event\Iface) continue;
            if ($ignoreHidden && !array_key_exists($field->getName(), $array) && !$field instanceof Field\File) continue;
            $field->load($array);
            //$field->setValue($array);
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
    public function load($array)
    {
        if ($this->loadArray === null) $this->loadArray = array();
        if (is_array($array)) {
            $this->loadArray = array_merge($this->loadArray, $array);
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
            $a = array();
            foreach($array as $k => $v) $a[$k] = $v;
            $array = $a;
        }
        // TODO: This could be removed, Check this out to be sure....????
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
     * @param string $fieldName
     * @param callable $callback
     * @return Event\Iface
     * @throws Form\Exception
     */
    public function setEventCallback($fieldName, $callback)
    {
        $fieldName = str_replace('[]', '', $fieldName);
        $field = $this->getField($fieldName);
        if (!$field || !$field instanceof Event\Iface) {
            throw new Form\Exception('Event Field not found: `' . $fieldName . '`');
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
        $fieldName = str_replace('[]', '', $fieldName);
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
        $fieldName = str_replace('[]', '', $fieldName);
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
     * @param null|string $regex
     * @return array
     */
    public function getValues($regex = null)
    {
        $array = array();
        /* @var $field Field\Iface */
        foreach ($this->getFieldList() as $name => $field) {
            if ($field instanceof Event\Iface) continue;
            if ($regex && !preg_match($regex, $name)) continue;
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
