<?php
namespace Tk\Form\Field;

/**
 * @see http://www.w3schools.com/tags/tag_option.asp
 */
class OptionGroup extends Option
{
    use OptionList;


    public function __construct(string $name, string $value = '')
    {
        parent::__construct($name, $value);
    }

    public function getValue(): string
    {
        return '';
    }

    public function setValue(string $value): static
    {
        return $this;
    }
}