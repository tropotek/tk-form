<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Template;
use Tk\Form\Exception;
use Tk\Form\Field\FieldInterface;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class Input extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        $field = $this->getField();
        if (!($field instanceof FieldInterface)) {
            throw new Exception("Invalid field renderer selected");
        }

        if (!(is_array($field->getValue()) || is_object($field->getValue()))) {
            $field->setAttr('value', $field->getValue());
        }
        $this->decorate();

        return $template;
    }

}