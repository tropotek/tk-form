<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Template;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class Textarea extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();
        // no type needed for textarea
        $this->getField()->removeAttr('type');

        if (!is_array($this->getField()->getValue()) && !is_object($this->getField()->getValue())) {
            $template->setText('element', strval($this->getField()->getValue()));
        }

        $this->decorate();

        return $template;
    }

}