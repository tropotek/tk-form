<?php
namespace Tk\Form\Field;

class Radio extends Checkbox
{

    /**
     * @param array<int|string,string> $optionList
     */
    public function __construct(string $name, array $optionList = [])
    {
        parent::__construct($name, $optionList, self::TYPE_RADIO);
    }

    /**
     * The value in a string format
     */
    public function setValue(mixed $value): static
    {
        $this->value = $value;
        $this->clearSelected();

        /** @var Option $option */
        foreach ($this->getOptions() as $option) {
            if ($this->isStrict()) {
                $option->setSelected(($option->getValue() === $value));
            } else {
                $option->setSelected(($option->getValue() == $value));
            }
        }
        return $this;
    }

    /**
     * Radio box groups do not select multiple values by default
     */
    public function setMultiple(bool $multiple = true): static
    {
        return parent::setMultiple(false);
    }

}