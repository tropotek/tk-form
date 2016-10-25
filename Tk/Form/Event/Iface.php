<?php
namespace Tk\Form\Event;

use Tk\Form\Exception;
use Tk\Form\Field;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
abstract class Iface extends Field\Iface
{
    /**
     * @var callable
     */
    protected $callback = null;


    /**
     * __construct
     *
     * @param string $name
     * @param callable $callback
     */
    public function __construct($name, $callback = null)
    {
        if ($callback) {
            $this->setCallback($callback);
        }
        parent::__construct($name);
    }

    /**
     * getEvent
     *
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * setEvent
     *
     * @param callable $callback
     * @return $this
     * @throws Exception
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            if (is_array($callback) && !empty($callback[1])) {
                $class = get_class($callback[0]);
                $method = $callback[1];
                throw new Exception('Form event callback ' . $class.'::'.$method.'() cannot be found');
            } else if (is_string($callback)) {
                throw new Exception('Form event callback ' . $callback . ' cannot be found');
            }
            throw new Exception('Only callable values can be events. Check the method or function exists.');
        }
        $this->callback = $callback;
        return $this;
    }

    
    
    // Force sain values for events below.


    /**
     * Get the field value(s).
     *
     * @return string|array
     */
    public function getValue()
    {
        return $this->getName();
    }

    /**
     * isRequired
     *
     * @return boolean
     */
    public function isRequired()
    {
        return false;
    }


    /**
     * Does this fields data come as an array.
     * If the name ends in [] then it will be flagged as an arrayField.
     *
     * EG: name=`name[]`
     *
     * @return boolean
     */
    public function isArrayField()
    {
        return false;
    }


    /**
     * @return string
     */
    public function getFieldset()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getTabGroup()
    {
        return '';
    }
    
}