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
class Money extends Iface
{

    /**
     * Convert the basic form submitted string field value
     * into its correct complex type.
     *
     * @param array|\stdClass $array
     * @return mixed
     */
    public function toObject($array)
    {
        $value = $array[$this->getFieldName()];
        if ($value !== '' && $value !== null ) {
            return \Tk\Money::parseFromString($value);
        }
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
        return $array[$this->getFieldName()]->toFloatString();
    }
    

}