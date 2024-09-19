<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Renderer\Traits\RendererTrait;
use Dom\Template;
use Tk\Form\Field\Option;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class Checkbox extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        /* @var Option $option */
        foreach($this->getField()->getOptions() as $option) {
            $tOpt = null;
            $tOpt = $template->getRepeat('option');
            $this->showOption($tOpt, $option);
            $tOpt->appendRepeat();
        }

        $this->decorate();

        return $template;
    }

    protected function showOption(Template $template, Option $option, string $var = 'option'): void
    {
        if ($this->getField()->isSwitch()) {
            $template->addCss($var, 'form-switch');
        }

        if ($this->getField()->getOnShowOption()->isCallable()) {
            $b = $this->getField()->getOnShowOption()->execute($template, $option, $var);
            if ($b === false) return;
        }
        if ($option->isSelected()) {
            $option->setAttr($option->getSelectAttr());
        }

        if ($this->getField()->isReadonly()) {
            $option->setAttr('readonly', 'readonly');
        }
        if ($this->getField()->isDisabled()) {
            $option->setAttr('disabled', 'disabled');
        }
        $option->setAttr('name', $this->getField()->getHtmlName());
        $option->setAttr('value', $option->getValue());

        $template->setText('label', $option->getName());
        $id = $this->getField()->getId().'-'.$this->getField()->cleanName($option->getName());
        $template->setAttr('label', 'for', $id);
        $option->setAttr('id', $id);

        if (!empty($this->optionNotes[$option->getValue()])) {
            $template->setVisible('notes');
            $template->setHtml('notes', $this->optionNotes[$option->getValue()]);
        }

        $template->setAttr('shadow', 'name', $this->getField()->getHtmlName());
        $template->setAttr('element', $option->getAttrList());
        $template->addCss('element', $option->getCssString());
    }

}