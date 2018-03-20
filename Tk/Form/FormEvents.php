<?php
namespace Tk\Form;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class FormEvents
{

    /**
     * @event \Tk\Event\FormEvent
     */
    const FORM_INIT = 'form.init';

    /**
     * @event \Tk\Event\FormEvent
     */
    const FORM_LOAD = 'form.load';

    /**
     * @event \Tk\Event\FormEvent
     */
    const FORM_SUBMIT = 'form.submit';


}