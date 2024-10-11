<?php

namespace Tk\Form\Renderer\Std\Field;

use Tk\Form\Field\FieldInterface;
use Tk\Form\Renderer\Std\FieldRendererInterface;

class Input extends FieldRendererInterface
{

    function show(array $data = []): string
    {
        $field = $this->getField();
        if ($field instanceof FieldInterface) {
            if (!(is_array($field->getValue()) || is_object($field->getValue()))) {
                $field->setAttr('value', $field->getValue());
            }
        }

        $data = $this->decorate($data);
        return $this->getTemplate()->parse($data);
    }

}