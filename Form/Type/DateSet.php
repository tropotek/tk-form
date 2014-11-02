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
class DateSet extends Iface
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
        $n = $name.'From';

        if (array_key_exists($n, $array)) {
            $this->fieldValues[$n] = trim($array[$n]);
            if (preg_match('/^([0-9]{1,2})(\/|-)([0-9]{1,2})(\/|-)([0-9]{2,4})( ([0-9]{2}):([0-9]{2})(:([0-9]{2))?)?$/', $this->fieldValues[$n])) {
                $val[$n] = \Tk\Date::create($this->fieldValues[$n]);
            }
        }
        $n = $name.'To';
        if (array_key_exists($n, $array)) {
            $this->fieldValues[$n] = trim($array[$n]);
            if (preg_match('/^([0-9]{1,2})(\/|-)([0-9]{1,2})(\/|-)([0-9]{2,4})( ([0-9]{2}):([0-9]{2})(:([0-9]{2))?)?$/', $this->fieldValues[$n])) {
                $val[$n] = \Tk\Date::create($this->fieldValues[$n]);
            }
        }
        $this->value = $val;
        return $this;
    }

    /**
     * This array will have objects that need to
     * be converted to strings for functions
     * like Form::loadFromObject() or Form::loadFromArray() etc...
     *
     * @param array|stdClass $array
     * @return $this
     */
    public function loadFromObjectArray($array)
    {
        $name = $this->getFieldName();
        $val = array();
        
        $n = $name.'From';
        if (array_key_exists($n, $array)) {
            $val[$n] = $array[$n];
            if ($array[$n] instanceof \Tk\Date) {
                $val[$n] = $array[$n]->format('d/m/Y');
                $this->fieldValues[$n] = $val[$n];
            }
        }
        $n = $name.'To';
        if (array_key_exists($n, $array)) {
            $val[$n] = $array[$n];
            if ($array[$n] instanceof \Tk\Date) {
                $val[$n] = $array[$n]->format('d/m/Y');
                $this->fieldValues[$n] = $val[$n];
            }
        }
        $this->value = $val;
        return $this;
    }



}