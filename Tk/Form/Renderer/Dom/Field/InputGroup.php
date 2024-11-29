<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Template;
use Tk\Form\Exception;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class InputGroup extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        $field = $this->getField();
        if (!($field instanceof \Tk\Form\Field\InputGroup)) {
            throw new Exception("Invalid field renderer selected");
        }

        if ($field->getPreText()) {
            $template->appendHtml('pre', $field->getPreText());
            $template->setVisible('pre');
        }

        if ($field->getPostText()) {
            $template->appendHtml('post', $field->getPostText());
            $template->setVisible('post');
        }

        // Render Element
        if (!(is_array($field->getValue()) || is_object($field->getValue()))) {
            $this->getField()->setAttr('value', $field->getValue());
        }

        $this->decorate();

        return $template;
    }

}