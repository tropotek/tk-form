<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Template;
use Tk\Form\Exception;
use Tk\Form\Field\FieldInterface;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class Html extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        $field = $this->getField();
        if (!($field instanceof FieldInterface)) {
            throw new Exception("Invalid field renderer selected");
        }

        $template->setHtml('element', $field->getValue());

        $this->decorate();

        return $template;
    }

}