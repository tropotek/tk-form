<?php

namespace Tk\Form\Renderer\Std\Field;

use Tk\Form\Exception;
use Tk\Form\Field\Option;
use Tk\Form\Renderer\Std\FieldRendererInterface;

class Checkbox extends FieldRendererInterface
{

    function show(array $data = []): string
    {
        /** @var \Tk\Form\Field\Checkbox $field */
        $field = $this->getField();
        if (!($field instanceof \Tk\Form\Field\Checkbox)) {
            throw new Exception("Invalid field renderer selected");
        }

        /* @var Option $option */
        foreach($field->getOptions() as $option) {
            $data['optionsBlock'][] = $this->showOption($option);
        }

        $data = $this->decorate($data);

        return $this->getTemplate()->parse($data);
    }

    protected function showOption(Option $option): array
    {
        /** @var \Tk\Form\Field\Checkbox $field */
        $field = $this->getField();
        if (!($field instanceof \Tk\Form\Field\Checkbox)) {
            throw new Exception("Invalid field renderer selected");
        }

        if ($field->getOnShowOption()->isCallable()) {
            $b = $field->getOnShowOption()->execute($field, $option);
            if ($b === false) return [];
        }
        if ($option->isSelected()) {
            $option->setAttr($option->getSelectAttr());
        }
        if ($field->isReadonly()) {
            $option->setAttr('readonly', 'readonly');
        }
        if ($field->isDisabled()) {
            $option->setAttr('disabled', 'disabled');
        }
        $option->setAttr('name', $field->getHtmlName());
        $option->setAttr('value', $option->getValue());

        $id = $field->getId().'-'.$field->cleanName($option->getName());
        $option->setAttr('id', $id);

        $optionCss = '';
        if ($field->isSwitch()) {
            $optionCss .= 'form-switch ';
        }

        $data = [
            'optionCss' => $optionCss,
            'shadowName' => $field->getHtmlName(),
            'css' => $option->getCssString(),
            'attrs' => $option->getAttrString(),
            'id' => $id,
            'label' => $option->getName(),
        ];

        if (!empty($this->optionNotes[$option->getValue()])) {
            $data['noteBlock'][] = [
                'notes' => sprintf('<p class="m-0 cb-notes text-muted">%s</p>', $this->optionNotes[$option->getValue()] ?? '')
            ];
        }

        return $data;
    }

}