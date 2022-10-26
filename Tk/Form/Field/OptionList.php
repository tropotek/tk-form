<?php

namespace Tk\Form\Field;

/**
 * @author Tropotek <http://www.tropotek.com/>
 */
trait OptionList
{
    /**
     * @var array|Option[]
     */
    protected array $options = [];


    public function setOptions(array $options): static
    {
        $this->options = $options;
        return $this;
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

    public function getValue(): string
    {
        return '';
    }
}