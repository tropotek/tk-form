<?php
namespace Tk\Form\Field;

use Tk\Form\Type;

/**
 * Class Text
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Event extends Iface
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
        parent::__construct($name, new Type\Null());
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
     * @throws \Tk\Form\Exception
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \Tk\Form\Exception('Only callable values can be events');
        }
        $this->callback = $callback;
        return $this;
    }

}