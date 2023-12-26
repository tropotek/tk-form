<?php

namespace Tk\Form\Renderer\Std\Action;

class Link extends Submit
{
    function show(array $data = []): string
    {
        $this->getField()->setAttr('href', $this->getField()->getUrl());
        return parent::show($data);
    }
}