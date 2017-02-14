<?php
namespace Tk\Form\Field;

use Tk\Form\Exception;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Select extends Iface
{
    
    /**
     * @var array|Option[]
     */
    protected $options = array();

    
    /**
     * @param string $name
     * @param Option\ArrayIterator|array $optionIterator
     */
    public function __construct($name, $optionIterator = null)
    {
        parent::__construct($name);
        if (is_array($optionIterator)) {
            $optionIterator = new Option\ArrayIterator($optionIterator);
        }
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
            $this->append($option);
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
            $this->prepend($option);
        }
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
     * @param $name
     * @param string $value
     * @param string $cssClass
     * @return Select
     */
    public function prependOption($name, $value = '', $cssClass = '')
    {
        $opt = new Option($name, $value);
        if ($cssClass) $opt->addCssClass($cssClass);
        return $this->prepend($opt);
    }

    /**
     * @param $name
     * @param string $value
     * @param string $cssClass
     * @return Select
     */
    public function appendOption($name, $value = '', $cssClass = '')
    {
        $opt = new Option($name, $value);
        if ($cssClass) $opt->addCssClass($cssClass);
        return $this->append($opt);
    }
    
    /**
     * @param Option $option
     * @return Select
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


    public function load($values)
    {
        parent::load($values);
        if (!isset($values[$this->getName()])) {
            $this->setValue('');
            if ($this->isArrayField())
                $this->setValue(array());
        }
        return $this;
    }

    /**
     * Compare a value and see if it is selected.
     *
     * @param string $val
     * @return bool
     */
    public function isSelected($val = '')
    {
        $value = $this->getValue();
        if (is_array($value) ) {
            if (in_array($val, $value)) {
                return true;
            }
        } else {
            //if ($val !== null && $value == $val) {
            if ($value !== null && $value == $val) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        $t = $this->getTemplate();
        if (!$t->keyExists('var', 'element')) {
            return $t;
        }

        if ($this->isArrayField()) {
            $t->setAttr('element', 'multiple', 'multiple');
        }


        /* @var \Tk\Form\Field\Option $option */
        foreach($this->getOptions() as $option) {
            /* @var \Dom\Repeat $tOpt */
            $tOpt = $t->getRepeat('option');

            if ($option->isDisabled()) {
                $tOpt->setAttr('option', 'disabled', 'disabled');
            }
            if ($option->getLabel()) {
                $tOpt->setAttr('option', 'label', $option->getLabel());
            }
            
            // TODO: render optgroup

            $tOpt->setAttr('option', 'value', $option->getValue());
            if ($this->isSelected($option->getValue())) {
                $tOpt->setAttr('option', 'selected', 'selected');
            }
            $tOpt->insertText('option', $option->getText());



            // Add attributes
            foreach($option->getAttrList() as $key => $val) {
                if ($val === '' || $val === null) $val = $key;
                $tOpt->setAttr('option', $key, $val);
            }

            // Add css class
            foreach($option->getCssClassList() as $v) {
                $tOpt->addCss('option', $v);
            }

            $tOpt->appendRepeat();
        }

        $this->decorateElement($t);
        return $t;
    }
    
    

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<select var="element" class="form-control">
  <option repeat="option" var="option"></option>
</select>
HTML;
        
        return \Dom\Loader::load($xhtml);
    }
}