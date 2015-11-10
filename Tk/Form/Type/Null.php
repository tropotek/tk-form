<?php
namespace Tk\Form\Type;


/**
 * Class Null
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Null extends Iface
{




    /**
     * Convert the basic form submitted string field value
     * into its correct complex type.
     *
     * @param array|\stdClass $array
     * @return mixed
     */
    public function toType($array)
    {
        return '';
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
        return '';
    }

    /**
     * return the fiel info array()
     *
     * @alias getFileInfo()
     * @return array
     */
    public function getValue()
    {
        return '';
    }



}
