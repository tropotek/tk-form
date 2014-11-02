<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Type;

/**
 *  A type object converts form element values to required types.
 *
 *
 * @package Form\Type
 */
class Boolean extends Iface
{

    /**
     * Convert the basic form submitted string field value
     * into its correct complex type.
     *
     * @param array|stdClass $array
     * @return bool
     */
    public function toObject($array)
    {
        if (!isset($array[$this->getFieldName()])) return false;
        $value = trim($array[$this->getFieldName()]);
        if ($value === $this->getFieldName()) {
            return true;
        }
        return false;
    }

    /**
     * Convert the field's complex type into
     * a string for the required field
     *
     * @param array|stdClass $array
     * @return string
     */
    public function toForm($array)
    {
        if (isset($array[$this->getFieldName()]) && $array[$this->getFieldName()] === true) {
            return $this->getFieldName();
        }
        return null;
    }




    /**
     * Load the field value object from a data sorce array.
     * This is usually, but not limited to, the $_REQUEST
     * $_GET or $_POST array's
     *
     * @param array $array
     */
    public function loadFromFormArray($array)
    {
        $name = $this->getFieldName();
        $valArr = array();

        //if (!isset($array[$name])) return $this;
        
        
        if ($this->field->hasArrayData() && is_array($array[$name])) {
            foreach ($array[$name] as $i => $v) {
                $valArr[] = $this->toObject( array($name => $v) );
            }
        } else {
            $valArr = $this->toObject($array);
        }
        $this->fieldValues[$name] = (isset($array[$name]) ? $array[$name] : '');
        $this->value = $valArr;
        return $this;
    }

    /**
     * This array will have objects that need to
     * be converted to strings for functions
     * like Form::loadFromObject() or Form::loadFromArray() etc...
     *
     * @param array|\stdClass $array
     */
    public function loadFromObjectArray($array)
    {
        $name = $this->getFieldName();
        $valArr = array();
        
        if (!isset($array[$name])) return $this;

        if ($this->field->hasArrayData() && is_array($array[$name])) {
            foreach ($array[$name] as $i => $v) {
                $valArr[] = $this->toForm( array($name => $v) );
            }
        } else {
            $valArr = $this->toForm($array);
        }
        $this->fieldValues[$name] = $valArr;
        $this->value = (isset($array[$name]) ? true : false);
        return $this;
    }
}