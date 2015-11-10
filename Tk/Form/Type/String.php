<?php
namespace Tk\Form\Type;


/**
 * Class String
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class String extends Iface {



    /**
     * Convert the basic form submitted string field value
     * into its correct complex type.
     *
     * @param array|\stdClass $array
     * @return mixed
     */
    public function toType($array)
    {
        return trim($array[$this->getField()->getName()].'');
    }

    /**
     * Convert the field's complex type into
     * a string for the required field
     *
     * @param array|\stdClass $array
     * @return string
     */
    public function toText($array)
    {
        $val = trim($array[$this->getField()->getName()].'');
        return $val;
    }

}
