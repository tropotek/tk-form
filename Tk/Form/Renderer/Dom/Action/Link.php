<?php

namespace Tk\Form\Renderer\Dom\Action;

use Dom\Template;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class Link extends Submit
{
    function show(): ?Template
    {
        $template = parent::show();

        $template->setAttr('element', 'href', $this->getField()->getUrl());

        return $template;
    }
}