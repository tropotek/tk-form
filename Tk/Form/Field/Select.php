<?php
namespace Tk\Form\Field;

use function PHPSTORM_META\type;
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
     * take a single dimensinoal array and convert it to a list for the select
     *
     * Input example:
     *     array('test', 'twoWord', 'three_word_test', 'another test');
     * Output:
     *     array('Test' => 'test', 'Two Word' => 'twoWord', 'Three Word Test' => 'three_word_test', 'Another Test' => 'another test')
     *
     *
     * @param $arr
     * @return array
     */
    public static function arrayToSelectList($arr, $modify = true)
    {
        //$arr = array('test', 'twoWord', 'three_word_test', 'another test');
        $new = array();
        foreach ($arr  as $v) {
            $n = $v;
            if ($modify) {
                $n = preg_replace('/[^A-Z0-9]/i', ' ', $n);
                $n = preg_replace('/[A-Z]/', ' $0', $n);
                $n = ucwords($n);
            }
            $new[$n] =  $v;
        }
        return $new;
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
        if ($cssClass) $opt->addCss($cssClass);
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
        if ($cssClass) $opt->addCss($cssClass);
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
        if ($this->getForm()->isSubmitted() && !array_key_exists($this->getName(), $values)) {
            $this->setValue(null);
        }
        parent::load($values);
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
            if ($val !== null && $value == $val) {
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
    public function show()
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
            $tOpt->setAttr('option', $option->getAttrList());

            // Add css class
            $tOpt->addCss('option', $option->getCssString());

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