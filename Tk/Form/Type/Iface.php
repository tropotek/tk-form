<?php
namespace Tk\Form\Type;

use Tk\Form\Field;

/**
 * A type object converts form element values to required types.
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
abstract class Iface
{

    /**
     * @var Field\Iface
     */
    protected $field = null;

    /**
     * An array of all field and sub-field string values from the request
     * @var array
     */
    protected $textValue = array();

    /**
     * An complex type representing this field's value
     * @var mixed
     */
    protected $typeValue = null;



    /**
     * @return Field\Iface
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param Field\Iface $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * Set the complex type value of the field,
     *
     * This method expects the value to be in
     * its fields complex type,
     *
     * IE:
     *    \Tk\Form\Type\Integer The value must be a number (14)
     *    \Tk\Form\Type\Date    The value must be a \DateTime object
     *
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $array = array($this->getField()->getName() => $value);
        $this->loadFromType($array);
        return $this;
    }

    /**
     * Get the field value based on its type.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->typeValue;
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
    public function getTextValue()
    {
        return $this->textValue;
    }




    /**
     * Convert the basic form submitted string field value
     * into its correct complex type.
     *
     * @param array|\stdClass $array
     * @return mixed
     */
    public abstract function toType($array);

    /**
     * Convert the field's complex type into
     * a string for the required field
     *
     * @param array|\stdClass $array
     * @return string
     */
    public abstract function toText($array);



    /**
     * Load the field value object from a data source array.
     * This is usually, but not limited to, the $_REQUEST
     * $_GET or $_POST array's
     *
     * NOTE: Override this method if you are using multiple fields
     *
     * @param array $strArr
     * @return $this
     */
    public function loadFromText($strArr)
    {
        $name = $this->getField()->getName();
        $typeArr = array();

        if (!isset($strArr[$name])) return $this;
        if ($this->getField()->isArray() && is_array($strArr[$name])) {
            foreach ($strArr[$name] as $i => $v) {
                $typeArr[$i] = $this->toType(array($name => $v));
            }
        } else {
            $typeArr = $this->toType($strArr);
        }
        $this->typeValue = $typeArr;
        $this->textValue[$name] = $strArr[$name];

        return $this;
    }

    /**
     * This array will have objects that need to
     * be converted to strings from their complex types
     *
     * NOTE: Override this method if you are using multiple complex types.
     *
     * @param array $typeArr
     * @return $this
     */
    public function loadFromType($typeArr)
    {
        $name = $this->getField()->getName();
        $strArr = array();

        if (!isset($typeArr[$name])) return $this;
        if ($this->getField()->isArray() && is_array($typeArr[$name])) {
            foreach ($typeArr[$name] as $i => $v) {
                $strArr[$i] = $this->toText(array($name => $v));
            }
        } else {
            $strArr = $this->toText($typeArr);
        }
        $this->textValue[$name] = $strArr;
        $this->typeValue = $typeArr[$name];

        return $this;
    }

}