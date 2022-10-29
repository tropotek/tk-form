<?php
namespace Tk\Form\Field;


use Dom\Template;

/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class Input extends FieldInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        // Render Element
        $this->setAttr('name', $this->getHtmlName());
        $this->setAttr('id', $this->getId());
        $this->setAttr('type', $this->getType());
        if (!is_array($this->getValue()) && !is_object($this->getValue())) {
            $this->setAttr('value', $this->getValue());
        }

        if ($this->getNotes()) {
            $template->replaceHtml('notes', $this->getNotes());
        }
        if ($this->hasError()) {
            $template->replaceHtml('error', $this->getError());
            $this->addCss('is-invalid');
        }

        $this->getOnShow()?->execute($template, $this);

        // Add any attributes
        $template->setAttr('element', $this->getAttrList());
        $template->addCss('element', $this->getCssList());

        // Render Label
        $template->setText('label', $this->getLabel());
        $template->setAttr('label', 'for', $this->getId());

        return $template;
    }
}