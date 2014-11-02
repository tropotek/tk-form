<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Event;

/**
 * Basic form events are executed on each form submit. The Event object
 * needs to check the event name that has been triggered itself [See Form::getExecutedEvent()].
 *
 *
 * @package Form\Event
 * @see Form::getExecutedEvent()
 */
abstract class Hidden extends \Form\Element implements Iface
{

    /**
     * __construct
     *
     * @param string $name
     */
    public function __construct($name = '')
    {
        if (!$name) {
            $name = 'Event-' . time();
        }
        $this->setName($name);
        $this->setLabel(self::makeLabel($name));
        $this->setHidden(true);
    }

    /**
     * Show
     */
    public function show() {}

}