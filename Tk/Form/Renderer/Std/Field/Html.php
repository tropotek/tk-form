<?php

namespace Tk\Form\Renderer\Std\Field;

use Tk\Form\Renderer\Std\FieldRendererInterface;

class Html extends FieldRendererInterface
{

    function show(array $data = []): string
    {
        $data = $this->decorate($data);
        $data['html'] = $this->getField()->getValue();

        return $this->getTemplate()->parse($data);
    }
    
}