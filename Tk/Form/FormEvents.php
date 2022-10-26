<?php
namespace Tk\Form;

/**
 * @author Tropotek <http://www.tropotek.com/>
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
    const FORM_SUBMIT = 'form.submit';

    /**
     * @event \Tk\Event\FormEvent
     */
    const FORM_SHOW = 'form.show';

}