<?php

namespace Tk\Form\Field;

/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
trait OptionList
{

    /**
     * @var array|Option[]
     */
    protected $options = array();



    /**
     * Set the options array
     *
     * @param array|Option[] $options
     * @return $this
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
     * @param $name
     * @param string $value
     * @param string $cssClass
     * @return $this
     */
    public function prependOption($name, $value = '', $cssClass = '')
    {
        $opt = new Option($name, $value);
        if ($cssClass) $opt->addCss($cssClass);
        return $this->prepend($opt);
    }

    /**
     * @param $name
     * @param string $value
     * @param string $cssClass
     * @return $this
     */
    public function appendOption($name, $value = '', $cssClass = '')
    {
        $opt = new Option($name, $value);
        if ($cssClass) $opt->addCss($cssClass);
        return $this->append($opt);
    }

    /**
     * @param Option $option
     * @return $this
     */
    public function append(Option $option)
    {
        $this->options[] = $option;
        return $this;
    }

    /**
     * @param Option $option
     * @return $this
     */
    public function prepend(Option $option)
    {
        array_unshift($this->options, $option);
        return $this;
    }



}