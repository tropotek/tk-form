<?php
namespace Tk\Form;

use Tk\Form;
use Tk\FormRenderer;

trait FormTrait
{
    protected ?Form $form = null;
    protected ?FormRenderer $formRenderer;


    public function setForm(Form $form): static
    {
        $this->form = $form;
        return $this;
    }

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setFormRenderer(FormRenderer $renderer): static
    {
        $this->formRenderer = $renderer;
        return $this;
    }

    public function getFormRenderer(): ?FormRenderer
    {
        return $this->formRenderer;
    }

}