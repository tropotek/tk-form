<?php

namespace Tk\Form\Renderer\Dom\Action;

use Dom\Template;
use Tk\Form\Exception;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class Link extends Submit
{
    function show(): ?Template
    {
        $template = parent::show();

        $field = $this->getField();
        if (!($field instanceof \Tk\Form\Action\Link)) {
            throw new Exception("Invalid field renderer selected");
        }

        $template->setAttr('element', 'href', $field->getUrl());

        return $template;
    }
}