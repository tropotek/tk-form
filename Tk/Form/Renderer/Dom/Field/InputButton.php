<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Template;
use Tk\Form\Exception;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class InputButton extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        $field = $this->getField();
        if (!($field instanceof \Tk\Form\Field\InputButton)) {
            throw new Exception("Invalid field renderer selected");
        }

        if ($field->getBtnText()) {
            $template->appendHtml('button', $field->getBtnText());
        }
        $template->setAttr('button', $field->getBtnAttr()->getAttrList());
        $template->addCss('button', $field->getBtnAttr()->getCssString());

        // Render Element
        if (!(is_array($field->getValue()) || is_object($field->getValue()))) {
            $this->getField()->setAttr('value', $field->getValue());
        }

        $this->decorate();

        return $template;
    }

}