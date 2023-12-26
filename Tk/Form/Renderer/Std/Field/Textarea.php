<?php

namespace Tk\Form\Renderer\Std\Field;

use Tk\Form\Renderer\Std\FieldRendererInterface;

class Textarea extends FieldRendererInterface
{

    function show(array $data = []): string
    {
        // no type needed for textarea
        $this->getField()->removeAttr('type');

        $data = $this->decorate($data);

        if (!is_array($this->getField()->getValue()) && !is_object($this->getField()->getValue())) {
            $data['value'] = $this->getField()->getValue() ?? '';
        }

        return $this->getTemplate()->parse($data);
    }
    
}