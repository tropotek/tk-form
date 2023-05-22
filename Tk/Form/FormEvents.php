<?php
namespace Tk\Form;

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
     * Called at the start of For::show()
     * @event \Tk\Event\FormEvent
     */
    const FORM_SHOW_PRE = 'form.show.pre';

    /**
     * Called at the end of Form::show()
     * @event \Tk\Event\FormEvent
     */
    const FORM_SHOW = 'form.show';

}