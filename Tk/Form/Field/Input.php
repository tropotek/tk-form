<?php
namespace Tk\Form\Field;

use Dom\Template;

class Input extends FieldInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        // Render Element
        $this->setAttr('name', $this->getHtmlName());
        $this->setAttr('id', $this->getId());
        $this->setAttr('type', $this->getType());
        if (!is_array($this->getValue()) && !is_object($this->getValue())) {
            $this->setAttr('value', $this->getValue());
        }

        $this->decorate($template);

        return $template;
    }
}