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
 * @package Form\Type
 */
class ArrayObject extends Iface
{

    /**
     * Convert the basic form submitted string field value
     * into its correct complex type.
     *
     * @param array|stdClass $array
     * @return array
     */
    public function toObject($array)
    {
        $value = $array[$this->getFieldName()];
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        return $value;
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
        $str = $array[$this->getFieldName()];
        if (is_array($str)) {
            $str = implode(',', $str);
        }
        return $str;
    }
    

}