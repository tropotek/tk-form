<?php
namespace Tk\Event;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class FormEvent extends Event
{

    /**
     * @var null|\Tk\Form
     */
    protected $form = null;

    /**
     * @param \Tk\Form $form
     */
    public function __construct($form)
    {
        $this->form = $form;
    }

    /**
     * @return null|\Tk\Form
     */
    public function getForm()
    {
        return $this->form;
    }

}