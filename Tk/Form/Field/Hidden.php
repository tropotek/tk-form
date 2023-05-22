<?php
namespace Tk\Form\Field;

use Dom\Template;

class Hidden extends FieldInterface
{

    public function __construct(string $name, string $value = '')
    {
        parent::__construct($name, self::TYPE_HIDDEN);
        $this->setValue($value);
        $this->setGroup(self::GROUP_NONE);
    }

    public function setFieldset(string $fieldset, array $attrs = null): static
    {
        $this->fieldset = '';
        return $this;
    }
    public function setGroup(string $group, array $attrs = null): static
    {
        $this->group = '';
        return $this;
    }

    function show(): ?Template
    {
        $template = $this->getTemplate();

        $this->setAttr('name', $this->getHtmlName());
        $this->setAttr('id', $this->getId());
        $this->setAttr('type', $this->getType());

        if (!is_array($this->getValue()) && !is_object($this->getValue())) {
            $this->setAttr('value', $this->getValue());
        }

        $this->decorate($template);

        return $template;
    }

}