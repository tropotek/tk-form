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

    public function prependOption(string $name, string $value = '', string $cssClass = ''): static
    {
        $opt = new Option($name, $value);
        if ($cssClass) $opt->addCss($cssClass);
        return $this->prepend($opt);
    }

    public function appendOption(string $name, string $value = '', string $cssClass = ''): static
    {
        $opt = new Option($name, $value);
        if ($cssClass) $opt->addCss($cssClass);
        return $this->append($opt);
    }

    public function append(Option $option): static
    {
        $this->options[] = $option;
        return $this;
    }

    public function prepend(Option $option): static
    {
        array_unshift($this->options, $option);
        return $this;
    }
}