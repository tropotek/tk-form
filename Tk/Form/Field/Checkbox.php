<?php
namespace Tk\Form\Field;

use Dom\Template;
use Tk\Db\Mapper\Result;
use Tk\Form\Field\Option\ArrayIterator;

class Checkbox extends Select
{


    public function __construct(string $name, null|array|Result|ArrayIterator $optionIterator = null)
    {
        if (!$optionIterator) $optionIterator = [$name];
        parent::__construct($name, $optionIterator);
        if (count($optionIterator) > 1) $this->setMultiple(true);
        $this->setType(self::TYPE_CHECKBOX);
    }

    protected function createIterator(array|Result|ArrayIterator $optionIterator = null, string $nameParam = 'name', string $valueParam = 'id', string $selectAttr = 'checked'): ?Option\ArrayIterator
    {
        return parent::createIterator($optionIterator, $nameParam,  $valueParam, $selectAttr);
    }

    function show(): ?Template
    {
        $template = $this->getTemplate();

        // Render Element
        $this->setAttr('name', $this->getHtmlName());
        $this->setAttr('id', $this->getId());
        $this->setAttr('type', $this->getType());

        /* @var Option $option */
        foreach($this->getOptions() as $option) {
            $tOpt = null;
            $tOpt = $template->getRepeat('option');
            $this->showOption($tOpt, $option);
            $tOpt->appendRepeat();
        }

        $this->decorate($template);

        // TODO: use the first option as the for attribute for the group label

        return $template;
    }

    protected function showOption(Template $template, Option $option, string $var = 'option'): void
    {
        if ($this->getOnShowOption()->isCallable()) {
            $b = $this->getOnShowOption()->execute($template, $option, $var);
            if ($b === false) return;
        }
        if ($option->isSelected()) {
            $option->setAttr($option->getSelectAttr());
        }
        if ($this->isReadonly()) {
            $option->setAttr('readonly', 'readonly');
        }
        if ($this->isDisabled()) {
            $option->setAttr('disabled', 'disabled');
        }
        $option->setAttr('name', $this->getHtmlName());
        $option->setAttr('value', $option->getValue());
        //$option->setAttr('value', $option->getName());

        $template->setText('label', $option->getName());
        $id = $this->getId().'-'.$this->cleanName($option->getName());
        $template->setAttr('label', 'for', $id);
        $option->setAttr('id', $id);

        $template->setAttr('shadow', 'name', $this->getHtmlName());
        $template->setAttr('element', $option->getAttrList());
        $template->addCss('element', $option->getCssString());
    }
}