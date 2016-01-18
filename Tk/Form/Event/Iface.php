<?php
namespace Tk\Form\Event;

use Tk\Form\Element;
use Tk\Form\Exception;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
abstract class Iface extends Element
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
        $this->setName($name);
        if ($callback) {
            $this->setCallback($callback);
        }
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
            throw new Exception('Only callable values can be events');
        }
        $this->callback = $callback;
        return $this;
    }

}