<?php
namespace Tk\Form\Field;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 *
 * @link http://www.w3schools.com/tags/tag_option.asp
 */
class Option
{
    /**
     * @var bool
     */
    protected $disabled = false;

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var string
     */
    protected $value = '';

    /**
     * @var string
     */
    protected $text = '';


    /**
     * @param string $text
     * @param string $value
     * @param bool $disabled
     * @param string $label
     */
    public function __construct($text, $value = '', $disabled = false, $label = '')
    {
        $this->text = $text;
        $this->value = $value;
        $this->disabled = $disabled;
        $this->label = $label;
    }

    /**
     * Create an Option object
     * 
     * @param $text
     * @param string $value
     * @param bool|false $disabled
     * @param string $label
     * @return Option
     */
    static function create($text, $value = '', $disabled = false, $label = '') 
    {
        $opt = new self($text, $value, $disabled, $label);
        return $opt;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Specifies the value to be sent to a server
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Specifies that an option that should be disabled
     *
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * Specify an option that should be disabled
     *
     * @param $b
     * @return $this
     */
    public function setDisabled($b)
    {
        $this->disabled = $b;
        return $this;
    }

    /**
     * Specifies a shorter label for an option
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

}