<?php
namespace Tk\Form\Field;

use Tk\Form\Exception;

/**
 * Class
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class SelectList extends Input
{

    /**
     * @var array|Option[]
     */
    protected $options = array();



    /**
     * @param string $name
     * @param Option\ArrayIterator $optionIterator
     */
    public function __construct($name, Option\ArrayIterator $optionIterator = null)
    {
        parent::__construct($name);
        if ($optionIterator) {
            $this->appendOptionIterator($optionIterator);
        }
    }

    /**
     * @param Option\ArrayIterator $optionIterator
     * @return $this
     */
    public function appendOptionIterator(Option\ArrayIterator $optionIterator)
    {
        foreach($optionIterator as $option) {
            $this->appendOption($option);
        }
        return $this;
    }

    /**
     * @param Option\ArrayIterator $optionIterator
     * @return $this
     */
    public function prependOptionIterator(Option\ArrayIterator $optionIterator)
    {
        foreach($optionIterator as $option) {
            $this->prependOption($option);
        }
        return $this;
    }

    /**
     * @param Option $option
     * @return $this
     */
    public function appendOption(Option $option)
    {
        $this->options[] = $option;
        return $this;
    }

    /**
     * @param Option $option
     * @return $this
     */
    public function prependOption(Option $option)
    {
        array_unshift($this->options, $option);
        return $this;
    }

    /**
     * @param string $text
     * @param string $value
     * @param bool $disabled
     * @param string $label
     * @return $this
     */
    public function append($text, $value = '', $disabled = false, $label = '')
    {
        $this->appendOption(new Option($text, $value, $disabled, $label));
        return $this;
    }

    /**
     * @param string $text
     * @param string $value
     * @param bool $disabled
     * @param string $label
     * @return $this
     */
    public function prepend($text, $value = '', $disabled = false, $label = '')
    {
        $this->prependOption(new Option($text, $value, $disabled, $label));
        return $this;
    }

    /**
     * Set the options array
     * The option array is in the format of array(array('name' => 'value'), array('name', 'value'),  etc...);
     *   this format allows for duplicate name and values
     *
     * @param array|Option[] $options
     * @return $this
     * @throws Exception
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return array|Option[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Compare a value and see if it is selected.
     *
     * @param string $val
     * @return bool
     */
    public function isSelected($val = '')
    {
        if (!$val)
            $val = [$this->getName()];
        
        $values = $this->getType()->getTextValue();
        if (isset($values[$this->getName()]) && $values[$this->getName()] == $val) {
            return true;
        }
        return false;
    }

}