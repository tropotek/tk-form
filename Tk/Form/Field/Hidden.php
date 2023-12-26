<?php
namespace Tk\Form\Field;

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

}