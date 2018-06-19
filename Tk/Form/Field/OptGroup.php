<?php
namespace Tk\Form\Field;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 *
 * @see http://www.w3schools.com/tags/tag_option.asp
 */
class OptGroup extends Option
{
    use OptionList;


    /**
     * Specifies the value to be sent to a server
     *
     * @return string
     */
    public function getValue()
    {
        return '';
    }

}