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
        $template->setAttr('element', 'name', $this->getHtmlName());
        $template->setAttr('element', 'id', $this->getId());
        $template->setAttr('element', 'type', $this->getType());
        $template->setAttr('element', 'value', $this->getValue());

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