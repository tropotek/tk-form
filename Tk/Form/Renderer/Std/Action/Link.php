<?php

namespace Tk\Form\Renderer\Std\Action;

use Tk\Form\Exception;

class Link extends Submit
{
    function show(array $data = []): string
    {
        $field = $this->getField();
        if (!($field instanceof \Tk\Form\Action\Link)) {
            throw new Exception("Invalid field renderer selected");
        }

        $field->setAttr('href', $field->getUrl());

        return parent::show($data);
    }
}