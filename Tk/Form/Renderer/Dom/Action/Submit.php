<?php

namespace Tk\Form\Renderer\Dom\Action;

use Dom\Template;
use Tk\Form\Field\FieldInterface;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class Submit extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        $field = $this->getField();
        if ($field instanceof \Tk\Form\Action\Submit) {
            if ($field->getType() != FieldInterface::TYPE_LINK) {
                $field->setAttr('name', $field->getId());
                $field->setAttr('value', $field->getValue());
            }
            $field->setAttr('title', ucfirst($field->getValue()));

            $template->setText('text', $field->getLabel());
            if ($field->getIcon()) {
                if ($field->getIconPosition() == \Tk\Form\Action\Submit::ICON_LEFT) {
                    $template->setVisible('icon-l');
                    $template->addCss('icon-l', $field->getIcon());
                } else {
                    $template->setVisible('icon-r');
                    $template->addCss('icon-r', $field->getIcon());
                }
            } else {
                // this removed HTMX bug with tags in the button???
                $template->setText('element', $field->getLabel());
            }

            $field->getOnShow()->execute($template, $field);

            $template->setAttr('element', $field->getAttrList());
            $template->addCss('element', $field->getCssList());
        }

        return $template;
    }

}