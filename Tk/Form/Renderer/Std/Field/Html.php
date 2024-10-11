<?php

namespace Tk\Form\Renderer\Std\Field;

use Tk\Form\Exception;
use Tk\Form\Field\FieldInterface;
use Tk\Form\Renderer\Std\FieldRendererInterface;

class Html extends FieldRendererInterface
{

    function show(array $data = []): string
    {
        $data = $this->decorate($data);

        $field = $this->getField();
        if (!($field instanceof FieldInterface)) {
            throw new Exception("Invalid field renderer selected");
        }

        $data['html'] = $field->getValue();

        return $this->getTemplate()->parse($data);
    }

}