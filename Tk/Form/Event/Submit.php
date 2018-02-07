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
     * @throws \Tk\Form\Exception
     */
    public function __construct($name, $callback = null)
    {
        $this->setType('submit');
        parent::__construct($name, $callback);
        if ($name == 'save') {
            $this->setIcon('glyphicon glyphicon-refresh');
        } else if ($name == 'update') {
            $this->setIcon('glyphicon glyphicon-arrow-left');
        }
    }

    /**
     * @param $name
     * @return Submit
     * @throws \Tk\Form\Exception
     */
    public static function create($name, $callback = null)
    {
        return new self($name, $callback);
    }

}