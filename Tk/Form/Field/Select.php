<?php
namespace Tk\Form\Field;

use Tk\CallbackCollection;

class Select extends FieldInterface
{
    use OptionList;

    protected CallbackCollection $onShowOption;

    /**
     * Enable strict type checking for null, '', 0, false, etc
     */
    protected bool $strict = false;

    /**
     * @param array<int|string,string> $optionList
     */
    public function __construct(string $name, array $optionList = [], string $type = self::TYPE_SELECT)
    {
        $this->onShowOption = CallbackCollection::create();
        parent::__construct($name, $type);
        $this->initOptionList($optionList);
    }

    /**
     * @param array<int|string,string> $optionList
     */
    protected function initOptionList(array $optionList): void
    {
        $selectedAttr = match($this->getType()) {
            self::TYPE_SELECT => 'selected',
            default => 'checked',
        };

        foreach ($optionList as $value => $name) {
            // is optgroup array
            if (is_array($name)) {
                $optgroup = new OptionGroup(strval($value));
                foreach ($name as $v => $n) {
                    $opt = new Option($n, $v, $selectedAttr);
                    $opt->setOptgroup($optgroup);
                    $optgroup->append($opt);
                }
                $this->append($optgroup);
            } else {
                $this->append(new Option($name, strval($value), $selectedAttr));
            }
        }
    }

    public function getOnShowOption(): CallbackCollection
    {
        return $this->onShowOption;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function setStrict(bool $strict): static
    {
        $this->strict = $strict;
        return $this;
    }

    /**
     *  function (\Dom\Template $template, \Tk\Form\Field\Option $option, $var) { }
     */
    public function addOnShowOption(callable $callable, int $priority = CallbackCollection::DEFAULT_PRIORITY): static
    {
        $this->getOnShowOption()->append($callable, $priority);
        return $this;
    }

    protected function clearSelected(): static
    {
        /** @var Option $option */
        foreach ($this->getAllOptions() as $option) {
            $option->setSelected(false);
        }
        return $this;
    }

    /**
     * The value in a string format
     */
    public function setValue(mixed $value): static
    {
        if ($this->isMultiple() && empty($value)) {
            $value = [];
        }
        $this->value = $value;
        $this->clearSelected();

        /** @var Option $option */
        foreach ($this->getAllOptions() as $option) {
            if ($this->isMultiple()) {
                if (is_array($this->value) && in_array($option->getValue(), $this->value, $this->isStrict())) {
                    $option->setSelected();
                }
            } else {
                if ($this->isStrict()) {
                    if ($option->getValue() === $this->value) {
                        $option->setSelected();
                    }
                } else {
                    if (!empty($this->value) && $option->getValue() == $this->value) {
                        $option->setSelected();
                    }
                }
            }
        }

        return $this;
    }

    /**
     * The value in a string format
     */
    public function getValue(): string|array
    {
        return $this->value;
    }

}