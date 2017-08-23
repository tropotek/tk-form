<?php
namespace Tk\Form\Event;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Submit extends Button
{


    /**
     * __construct
     *
     * @param string $name
     * @param callable $callback
     */
    public function __construct($name, $callback = null)
    {
        parent::__construct($name, $callback);
        if ($name == 'save') {
            $this->setType('submit');
            $this->setIcon('glyphicon glyphicon-refresh');
        } else if ($name == 'update') {
            $this->setType('submit');
            $this->setIcon('glyphicon glyphicon-arrow-left');
        }
    }

}