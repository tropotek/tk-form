<?php
namespace Tk\Form;

/**
 * @author Michael Mifsud <http://www.tropotek.com/>
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
    const FORM_LOAD_REQUEST = 'form.load.request';

    /**
     * @event \Tk\Event\FormEvent
     */
    const FORM_SHOW = 'form.show';

    /**
     * @event \Tk\Event\FormEvent
     */
    const FORM_SUBMIT = 'form.submit';


}