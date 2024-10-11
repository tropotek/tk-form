<?php

namespace Tk\Form\Renderer\Std\Field;

use Tk\Form\Renderer\Std\FieldRendererInterface;

class Html extends FieldRendererInterface
{

    function show(array $data = []): string
    {
        $data = $this->decorate($data);

        $field = $this->getField();
        if ($field instanceof \Tk\Form\Field\Html) {
            $data['html'] = $field->getValue();
        }

        return $this->getTemplate()->parse($data);
    }

}