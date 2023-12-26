<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Template;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class InputButton extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        if ($this->getField()->getBtnText()) {
            $template->appendHtml('button', $this->getField()->getBtnText());
        }
        $template->setAttr('button', $this->getField()->getBtnAttr()->getAttrList());
        $template->addCss('button', $this->getField()->getBtnCss()->getCssString());

        $this->decorate();

        return $template;
    }
    
}