<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Template;
use Tk\Form\Exception;
use Tk\Form\Field\Option;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class Checkbox extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        $field = $this->getField();
        if (!($field instanceof \Tk\Form\Field\Checkbox)) {
            throw new Exception("Invalid field renderer selected");
        }

        /* @var Option $option */
        foreach($field->getOptions() as $option) {
            $tOpt = null;
            $tOpt = $template->getRepeat('option');
            $this->showOption($tOpt, $option);
            $tOpt->appendRepeat();
        }

        // ensure value for unselected checkboxes
        $template->setAttr('shadow', 'name', $field->getName());

        $this->decorate();

        return $template;
    }

    protected function showOption(Template $template, Option $option, string $var = 'option'): void
    {
        $field = $this->getField();
        if (!($field instanceof \Tk\Form\Field\Checkbox)) {
            throw new Exception("Invalid field renderer selected");
        }

        if ($field->isSwitch()) {
            $template->addCss($var, 'form-switch');
        }

        if ($field->getOnShowOption()->isCallable()) {
            $b = $field->getOnShowOption()->execute($template, $option, $var);
            if ($b === false) return;
        }

        if ($option->isSelected()) {
            $option->setAttr($option->getSelectedAttr());
        }

        if ($field->isReadonly()) {
            $option->setAttr('readonly', 'readonly');
        }
        if ($field->isDisabled()) {
            $option->setAttr('disabled', 'disabled');
        }
        $option->setAttr('name', $field->getHtmlName());
        $option->setAttr('value', $option->getValue());

        $template->setText('label', $option->getName());

        $id = $field->getId() . '-' . $field->cleanName($option->getName() ?: $option->getAttr('name'));
        $template->setAttr('label', 'for', $id);
        $option->setAttr('id', $id);

        if (!empty($this->optionNotes[$option->getValue()])) {
            $template->setVisible('notes');
            $template->setHtml('notes', $this->optionNotes[$option->getValue()]);
        }

        $template->setAttr('element', $option->getAttrList());
        $template->addCss('element', $option->getCssString());
    }

}