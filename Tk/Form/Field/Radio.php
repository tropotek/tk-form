<?php
namespace Tk\Form\Field;

use Tk\Db\Mapper\Result;
use Tk\Form\Field\Option\ArrayIterator;

class Radio extends Checkbox
{


    public function __construct(string $name, null|array|Result|ArrayIterator $optionIterator = null)
    {
        parent::__construct($name, $optionIterator);
        $this->setType(self::TYPE_RADIO);
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
    public function setMultiple(bool $multiple): static
    {
        return parent::setMultiple(false);
    }




}