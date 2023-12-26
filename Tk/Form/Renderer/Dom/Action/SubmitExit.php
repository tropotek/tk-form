<?php

namespace Tk\Form\Renderer\Dom\Action;

use Dom\Template;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class SubmitExit extends Submit
{

    function show(): ?Template
    {
        $template = parent::show();

        // Render Element
        $template->setAttr('exit', 'name', $this->getField()->getId());
        $template->setAttr('exit', 'value', $this->getField()->getValue().'-exit');
        $template->setAttr('exit', 'title', ucfirst($this->getField()->getValue()) . ' and exit');

        return $template;
    }
}