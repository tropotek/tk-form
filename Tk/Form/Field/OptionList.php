<?php
namespace Tk\Form\Field;

trait OptionList
{
    /**
     * @var array<int,Option>
     */
    protected array $options = [];


    /**
     * returns all options as a flat array, OptionGroups are removed
     */
    public function getAllOptions(): array
    {
        $options = [];
        foreach ($this->options as $option) {
            if ($option instanceof OptionGroup) {
                foreach ($option->getOptions() as $o) {
                    $options[] = $o;
                }
            } else {
                $options[] = $option;
            }
        }
        return $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function prependOption(string $name, ?string $value = null, bool $replace = false): static
    {
        $opt = new Option($name, $value ?? '');
        return $this->prepend($opt, $replace);
    }

    public function prepend(Option $option, bool $replace = false): static
    {
        if ($replace) {
            if (empty($option->getValue())) return $this;
            $idx = $this->getOptionValueIndex($option->getValue());
            if (!is_null($idx)) {
                $this->options[$idx] = $option;
                return $this;
            }
        }
        array_unshift($this->options, $option);

        return $this;
    }

    public function appendOption(string $name, ?string $value = null, bool $replace = false): static
    {
        $opt = new Option($name, $value ?? '');
        return $this->append($opt, $replace);
    }

    public function append(Option $option, bool $replace = false): static
    {
        if ($replace) {
            if (empty($option->getValue())) return $this;
            $idx = $this->getOptionValueIndex($option->getValue());
            if (!is_null($idx)) {
                $this->options[$idx] = $option;
                return $this;
            }
        }
        $this->options[] = $option;

        return $this;
    }

    protected function getOptionValueIndex(string $value): ?int
    {
        foreach ($this->options as $i => $o) {
            if ($value === $o->getValue()) {
                return $i;
            }
        }
        return null;
    }
}