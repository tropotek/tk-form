<?php
namespace Tk\Form;

use Tk\Form;

trait FormTrait
{
    protected ?Form $form = null;
    protected ?Form\Renderer\Dom\Renderer $formRenderer;


    public function setForm(Form $form): static
    {
        $this->form = $form;
        return $this;
    }

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setFormRenderer(Form\Renderer\Dom\Renderer $renderer): static
    {
        $this->formRenderer = $renderer;
        return $this;
    }

    public function getFormRenderer(): ?Form\Renderer\Dom\Renderer
    {
        return $this->formRenderer;
    }

}