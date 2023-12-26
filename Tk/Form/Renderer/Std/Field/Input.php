<?php

namespace Tk\Form\Renderer\Std\Field;

use Tk\Form\Renderer\Std\FieldRendererInterface;

class Input extends FieldRendererInterface
{

    function show(array $data = []): string
    {
        // Render Element
        if (!(is_array($this->getField()->getValue()) || is_object($this->getField()->getValue()))) {
            $this->getField()->setAttr('value', $this->getField()->getValue() ?? '');
        }

        $data = $this->decorate($data);
        return $this->getTemplate()->parse($data);
    }
    
}