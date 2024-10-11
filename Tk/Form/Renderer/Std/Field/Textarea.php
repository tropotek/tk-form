<?php

namespace Tk\Form\Renderer\Std\Field;

use Tk\Form\Field\FieldInterface;
use Tk\Form\Renderer\Std\FieldRendererInterface;

class Textarea extends FieldRendererInterface
{

    function show(array $data = []): string
    {
        // no type needed for textarea
        $this->getField()->removeAttr('type');
        $data = $this->decorate($data);

        $field = $this->getField();
        if ($field instanceof \Tk\Form\Field\Textarea) {
            if (!(is_array($field->getValue()) || is_object($field->getValue()))) {
                $data['value'] = $field->getValue();
            }
        }

        return $this->getTemplate()->parse($data);
    }

}