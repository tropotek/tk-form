<?php
namespace Tk\Form\Type;


/**
 * Class String
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Boolean extends Iface {


    /**
     * Convert the basic form submitted string field value
     * into its correct complex type.
     *
     * @param array|\stdClass $array
     * @return mixed
     */
    public function toType($array)
    {
        if (!isset($array[$this->getField()->getName()])) return false;
        $value = trim($array[$this->getField()->getName()]);
        if ($value === $this->getField()->getName()) {
            return true;
        }
        return false;
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
        if (isset($array[$this->getField()->getName()]) && ($array[$this->getField()->getName()] === true || (int)$array[$this->getField()->getName()] === 1)) {
            return $this->getField()->getName();
        }
        return null;
    }


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

        if ($this->getField()->isArray() && is_array($strArr[$name])) {
            foreach ($strArr[$name] as $i => $v) {
                $typeArr[$i] = $this->toType(array($name => $v));
            }
        } else {
            $typeArr = $this->toType($strArr);
        }
        $this->typeValue = $typeArr;
        $this->textValue[$name] = isset($strArr[$name]) ? $strArr[$name] : '';

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
        $this->typeValue = isset($typeArr[$name]);

        return $this;
    }
}
