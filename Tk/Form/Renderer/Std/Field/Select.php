<?php

namespace Tk\Form\Renderer\Std\Field;

use Tk\Form\Field\Option;
use Tk\Form\Field\OptionGroup;
use Tk\Form\Renderer\Std\FieldRendererInterface;

class Select extends FieldRendererInterface
{

    function show(array $data = []): string
    {
        /** @var \Tk\Form\Field\Select $field */
        $field = $this->getField();

        $optionsHtml = '';
        /* @var Option $option */
        foreach($field->getOptions() as $option) {
            if ($option instanceof OptionGroup) {
                $optionsHtml .= sprintf('<optgroup label="%s">', $option->getName());
                foreach ($option->getOptions() as $opt) {
                    $optionsHtml .= $this->showOption($opt);
                }
                $optionsHtml .= '</optgroup>';
            } else {
                $optionsHtml .= $this->showOption($option);
            }
        }

        $data = $this->decorate($data);
        $data['options'] = $optionsHtml;

        return $this->getTemplate()->parse($data);
    }

    protected function showOption(Option $option): string
    {
        /** @var \Tk\Form\Field\Select $field */
        $field = $this->getField();

        if ($field->getOnShowOption()->isCallable()) {
            $b = $field->getOnShowOption()->execute($field, $option);
            if ($b === false) return '';
        }
        if ($option->isSelected()) {
            $option->setAttr($option->getSelectAttr());
        }

        $css = $option->getCssString();
        if ($css) $css = sprintf('class="%s"', $css);

        return sprintf('<option %s %s>%s</option>',
            $css,
            $option->getAttrString(),
            $option->getName()
        );
    }
    
}