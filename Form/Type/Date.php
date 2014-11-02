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
class Date extends Iface
{



    /**
     * Convert the basic form submitted string field value
     * into its correct complex type.
     *
     * @param array|stdClass $array
     * @return mixed
     */
    public function toObject($array)
    {
        $value = $array[$this->getFieldName()];
        if (preg_match('/^([0-9]{1,2})(\/|-)([0-9]{1,2})(\/|-)([0-9]{2,4})( ([0-9]{2}):([0-9]{2})(:([0-9]{2))?)?$/', $value)) {
            return \Tk\Date::create($value);
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
        $value = $array[$this->getFieldName()];
        if ($value instanceof \Tk\Date)
            return $value->format('d/m/Y');
        return '';
    }


}