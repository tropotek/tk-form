<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Type;

/**
 * A type object converts form element values to required types.
 *
 * @package Form\Type
 */
abstract class Iface
{

    /**
     * An array of all sub-field values
     * Usually only one sub-field is used but needed
     * in-case multiple fields are used, eg: date/month/year or hh:mm
     * where each sub-value is required to create a single field/object
     *
     * @var array
     */
    protected $fieldValues = array();

    /**
     * An object/native type representing this field's value
     * @var mixed
     */
    protected $value = null;

    /**
     * @var \Form\Field\Iface
     */
    protected $field = null;



    /**
     * Convert the basic form submitted string field value
     * into its correct complex type.
     *
     * @param array|\stdClass $array
     * @return mixed
     */
    public function toObject($array)
    {
        return trim($array[$this->getFieldName()]);
    }

    /**
     * Convert the field's complex type into
     * a string for the required field
     *
     * @param array|\stdClass $array
     * @return string
     */
    public function toForm($array)
    {
        $val = $array[$this->getFieldName()];
        if (is_string($val))
            $val = trim($val);
        return $val;
    }


    /**
     * Load the field value object from a data source array.
     * This is usually, but not limited to, the $_REQUEST
     * $_GET or $_POST array's
     *
     * @param array $array
     * @return $this
     */
    public function loadFromFormArray($array)
    {
        $name = $this->getFieldName();
        $valArr = array();
        if (!array_key_exists($name, $array)) {
            return;
        }

        if ($this->field->hasArrayData() && is_array($array[$name])) {
            foreach ($array[$name] as $i => $v) {
                $valArr[] = $this->toObject( array($name => $v) );
            }
        } else {
            $valArr = $this->toObject($array);
        }

        $this->fieldValues[$name] = $array[$name];
        $this->value = $valArr;
        return $this;
    }

    /**
     * This array will have objects that need to
     * be converted to strings for functions
     * like Form::loadFromObject() or Form::loadFromArray() etc...
     *
     * @param array|\stdClass $array
     * @return $this
     */
    public function loadFromObjectArray($array)
    {
        $name = $this->getFieldName();
        $valArr = array();
        if (!array_key_exists($name, $array)) {
            return;
        }

        if ($this->field->hasArrayData() && is_array($array[$name])) {
            foreach ($array[$name] as $i => $v) {
                $valArr[] = $this->toForm( array($name => $v) );
            }
        } else {
            $valArr = $this->toForm($array);
        }
        $this->fieldValues[$name] = $valArr;
        $this->value = $array[$name];
        return $this;
    }


    /**
     * Set the value of the field,
     * This method expects the value to be in
     * its fields type, IE: if a \Tk\Date field then
     * the value must be a Tk\Date object...
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $array = array($this->getFieldName() => $value);
        $this->loadFromObjectArray($array);
        return $this;
    }

    /**
     * Get the field value based on its type.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Return the field values array
     * Override this method for more complex types.
     *
     * Generally this array has the field name and a value like so:
     *     array('field1' => 'Value1')
     * but for more complex fields like a map we can return an array like:
     *     array('mapLat' => '0.0', 'mapLng' => 0.0, 'mapZoom' => 0)
     *
     * @return array
     */
    public function getFieldValues()
    {
        return $this->fieldValues;
    }

    /**
     * Set the form field.
     *
     * @param \Form\Field\Iface $field
     */
    public function setField(\Form\Field\Iface $field)
    {
        $this->field = $field;
    }

    /**
     * Get the field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->field->getName();
    }

    /**
     * Convert an object/array to a string
     *
     * @param mixed $obj
     * @return string
     */
    protected function objToString($obj)
    {
        if (is_object($obj) && method_exists($obj, '__toString')) {
            return $obj->__toString();
        }
        if (is_array($obj)) {
            return implode(',', $obj);
        }
        return $obj;
    }

}