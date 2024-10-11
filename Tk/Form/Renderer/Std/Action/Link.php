<?php

namespace Tk\Form\Renderer\Std\Action;

class Link extends Submit
{
    function show(array $data = []): string
    {
        $field = $this->getField();
        if ($field instanceof \Tk\Form\Action\Link) {
            $field->setAttr('href', $field->getUrl());
        }
        return parent::show($data);
    }
}