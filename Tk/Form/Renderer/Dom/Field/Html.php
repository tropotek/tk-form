<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Template;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class Html extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        // Render Element
        $template->setHtml('element', $this->getField()->getValue());

        $this->decorate();

        return $template;
    }
    
}