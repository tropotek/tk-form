<?php
namespace Tk\Form\Field;

/**
 * @see http://www.w3schools.com/tags/tag_option.asp
 */
class OptionGroup extends Option
{
    use OptionList;


    static function create(string $name, string $value = '', string $selectAttr = 'selected'): self
    {
        return new self($name, $value, $selectAttr);
    }
}