<?php

namespace Tk\Form\Renderer\Dom\Action;

use Dom\Template;
use Tk\Form\Exception;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class SubmitExit extends Submit
{

    function show(): ?Template
    {
        $template = parent::show();

        $field = $this->getField();
        if (!($field instanceof \Tk\Form\Action\SubmitExit)) {
            throw new Exception("Invalid field renderer selected");
        }

        $template->setAttr('exit', 'name', $field->getId());
        $template->setAttr('exit', 'value', $field->getValue() . '-exit');
        $template->setAttr('exit', 'title', ucfirst($field->getValue()) . ' and exit');

        return $template;
    }
}