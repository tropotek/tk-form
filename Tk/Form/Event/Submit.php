<?php
namespace Tk\Form\Event;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Submit extends Button
{

    /**
     * @param string $name
     * @param callable $callback
     */
    public function __construct($name, $callback = null)
    {
        $this->setType('submit');
        parent::__construct($name, $callback);
        // TODO: These need to be removed
        if ($name == 'save') {
            $this->removeCss('btn-default');
            $this->addCss('btn-success');
            $this->setIcon('fa fa-refresh');
        } else if ($name == 'update') {
            $this->removeCss('btn-default');
            $this->addCss('btn-success');
            $this->setIcon('fa fa-arrow-left');
        }
    }

    /**
     * @param $name
     * @param null $callback
     * @return Submit
     * @throws \Tk\Form\Exception
     */
    public static function create($name, $callback = null)
    {
        return new self($name, $callback);
    }

}