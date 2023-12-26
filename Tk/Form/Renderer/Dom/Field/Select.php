<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Template;
use Tk\Form\Field\Option;
use Tk\Form\Field\OptionGroup;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class Select extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        /* @var Option $option */
        foreach($this->getField()->getOptions() as $option) {
            $tOpt = null;
            if ($option instanceof OptionGroup) {
                $tOptGroup = $template->getRepeat('optgroup');
                $tOptGroup->setAttr('optgroup', 'label', $option->getName());
                foreach ($option->getOptions() as $opt) {
                    $tOpt = $tOptGroup->getRepeat('option');
                    $this->showOption($tOpt, $opt);
                    $tOpt->appendRepeat();
                }
                $tOptGroup->appendRepeat();
            } else {
                $tOpt = $template->getRepeat('option');
                $this->showOption($tOpt, $option);
                $tOpt->appendRepeat();
            }
        }

        $this->decorate();

        return $template;
    }

    protected function showOption(Template $template, Option $option, string $var = 'option'): void
    {
        if ($this->getField()->getOnShowOption()->isCallable()) {
            $b = $this->getField()->getOnShowOption()->execute($template, $option, $var);
            if ($b === false) return;
        }
        if ($option->isSelected()) {
            $option->setAttr($option->getSelectAttr());
        }

        $template->setText($var, $option->getName());
        $template->setAttr($var, $option->getAttrList());
        $template->addCss($var, $option->getCssString());
    }
    
}