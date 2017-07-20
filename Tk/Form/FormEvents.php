<?php
namespace Tk\Form;



/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class FormEvents
{

    /**
     * @event \Tk\Event\Event
     */
    const FORM_INIT = 'form.init';

    /**
     * @event \Tk\Event\Event
     */
    const FORM_LOAD = 'form.load';

    /**
     * @event \Tk\Event\Event
     */
    const FORM_SUBMIT = 'form.submit';


}