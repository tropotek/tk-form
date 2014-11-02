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
class Gmap extends Iface
{

    /**
     * Load the field value object from a data sorce array.
     * This is usually, but not limited to, the $_REQUEST
     * $_GET or $_POST array's
     *
     * @param array $array
     * @return $this
     */
    public function loadFromFormArray($array)
    {
        $name = $this->getFieldName();
        $val = array();
        $n = $name.'Lat';
        if (array_key_exists($n, $array)) {
            $this->fieldValues[$n] = trim($array[$n]);
            $val[$n] = $array[$n];
        }
        $n = $name.'Lng';
        if (array_key_exists($n, $array)) {
            $this->fieldValues[$n] = trim($array[$n]);
            $val[$n] = $array[$n];
        }
        $n = $name.'Zoom';
        if (array_key_exists($n, $array)) {
            $this->fieldValues[$n] = trim($array[$n]);
            $val[$n] = $array[$n];
        }
        $this->value = $val;
        return $this;
    }

    /**
     * This array will have objects that need to
     * be converted to strings for functions
     * like \Form::loadFromObject() or Form::loadFromArray() etc...
     *
     * @param array|stdClass $array
     * @return $this
     */
    public function loadFromObjectArray($array)
    {
        $name = $this->getFieldName();
        $val = array();
        $n = $name.'Lat';
        if (array_key_exists($n, $array)) {
            $val[$n] = $array[$n];
            $this->fieldValues[$n] = $val[$n];
        }
        $n = $name.'Lng';
        if (array_key_exists($n, $array)) {
            $val[$n] = $array[$n];
            $this->fieldValues[$n] = $val[$n];
        }
        $n = $name.'Zoom';
        if (array_key_exists($n, $array)) {
            $val[$n] = $array[$n];
            $this->fieldValues[$n] = $val[$n];
        }
        $this->value = $val;
        return $this;
    }

    


}