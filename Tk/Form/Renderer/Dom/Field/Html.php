<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Template;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class Html extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        $field = $this->getField();
        if ($field instanceof \Tk\Form\Field\Input) {
            $template->setHtml('element', $field->getValue());
        }

        $this->decorate();

        return $template;
    }

}