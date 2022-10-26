<?php
namespace Tk\Form\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tk\Form;

/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class FormEvent extends Event
{

    protected Form $form;


    public function __construct($form)
    {
        $this->form = $form;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

}