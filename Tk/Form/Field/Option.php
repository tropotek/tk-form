<?php
namespace Tk\Form\Field;

use Tk\Ui\Traits\AttributesTrait;

/**
 * @see http://www.w3schools.com/tags/tag_option.asp
 */
class Option
{
    use AttributesTrait;

    protected string       $name         = '';
    protected string       $selectedAttr = '';
    protected ?OptionGroup $optgroup     = null;


    public function __construct(string $name, string $value, string $selectedAttr = 'selected')
    {
        $this->name = $name;
        $this->selectedAttr = $selectedAttr;
        $this->setValue($value);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getValue(): string
    {
        return $this->getAttr('value');
    }

    public function setValue(string $value): static
    {
        $this->setAttr('value', $value);
        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->hasAttr('disabled');
    }

    public function setDisabled(bool $b = true): static
    {
        if ($b) {
            $this->setAttr('disabled');
        } else {
            $this->removeAttr('disabled');
        }
        return $this;
    }

    public function isSelected(): bool
    {
        return $this->hasAttr($this->getSelectedAttr());
    }

    public function setSelected(bool $b = true): static
    {
        if ($b) {
            $this->setAttr($this->getSelectedAttr());
        } else {
            $this->removeAttr($this->getSelectedAttr());
        }
        return $this;
    }

    public function getSelectedAttr(): string
    {
        return $this->selectedAttr;
    }

    public function getOptgroup(): ?OptionGroup
    {
        return $this->optgroup;
    }

    public function setOptgroup(?OptionGroup $optgroup): Option
    {
        $this->optgroup = $optgroup;
        return $this;
    }

}