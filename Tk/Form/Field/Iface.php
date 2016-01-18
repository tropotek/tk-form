<?php
namespace Tk\Form\Field;

use Tk\Form\Exception;
use \Tk\Form;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
abstract class Iface extends \Tk\Form\Element
{

    /**
     * An array of all field and sub-field string for this field
     * @var array
     */
    protected $values = array();
    
    /**
     * @var bool
     */
    protected $required = false;

    /**
     * @var bool
     */
    protected $arrayField = false;
    

    /**
     * __construct
     *
     * @param string $name
     * @throws Exception
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * Set the field value(s)
     *
     * @param array|string $values
     * @return $this
     */
    public function setValue($values)
    {
        if (!is_array($values)) {
            $values = array($this->getName() => $values);
        }
        if (!isset($values[$this->getName()])) return $this;
   
        $this->values[$this->getName()] = $values[$this->getName()];

        return $this;
    }

    /**
     * Get the field value(s).
     * 
     * @return string|array
     */
    public function getValue()
    {
        if (isset($this->values[$this->getName()])) {
            return $this->values[$this->getName()];
        }
        return '';
    }

    /**
     * @return array
     */
    public function getValueArray() 
    {
        return $this->values;
    }

    /**
     * isRequired
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * setRequired
     *
     * @param boolean $required
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * Does this fields data come as an array.
     * If the name ends in [] then it will be flagged as an arrayField.
     *
     * EG: name=`name[]`
     *
     * @return boolean
     */
    public function isArray()
    {
        return $this->arrayField;
    }

    /**
     * Set to true if this element is an array set
     * 
     * @param $b
     * @return $this
     */
    public function setArrayField($b)
    {
        $this->arrayField = $b;
        return $this;
    }
    
}