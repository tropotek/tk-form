<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Template;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class Input extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        // Render Element
        if (!(is_array($this->getField()->getValue()) || is_object($this->getField()->getValue()))) {
            $this->getField()->setAttr('value', $this->getField()->getValue() ?? '');
        }

        $this->decorate();

        return $template;
    }
    
}