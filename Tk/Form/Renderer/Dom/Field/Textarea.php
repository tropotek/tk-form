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

        $field = $this->getField();
        if ($field instanceof \Tk\Form\Field\Textarea) {
            if (!is_array($field->getValue()) && !is_object($field->getValue())) {
                $template->setText('element', strval($field->getValue()));
            }
        }

        $this->decorate();

        return $template;
    }

}