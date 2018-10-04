<?php
namespace Tk\Form\Field;

use Tk\Form\Exception;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Select extends Iface
{
    use OptionList;

    /**
     * @var null|callable
     */
    protected $onShowOption = null;


    /**
     * @param string $name
     * @param null|Option\ArrayIterator|array|\Tk\Db\Map\ArrayObject $optionIterator
     */
    public function __construct($name, $optionIterator = null)
    {
        parent::__construct($name);

        if ($optionIterator instanceof \Tk\Db\Map\ArrayObject || (is_array($optionIterator) && current($optionIterator) instanceof \Tk\Db\ModelInterface)) {
            $optionIterator = new Option\ArrayObjectIterator($optionIterator);
        } else if (is_array($optionIterator)) {
            if (is_array(current($optionIterator))) {
                $optionIterator = new Option\ArrayArrayIterator($optionIterator);
            } else {
                $optionIterator = new Option\ArrayIterator($optionIterator);
            }
        }

        if ($optionIterator) {
            $this->appendOptionIterator($optionIterator);
        }
    }

    /**
     * @param string $name
     * @param Option\ArrayIterator|array|\Tk\Db\Map\ArrayObject $optionIterator
     * @return static
     * @throws Exception
     */
    public static function createSelect($name, $optionIterator = null)
    {
        return new static($name, $optionIterator);
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
     * @param bool $modify
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
     * @param array|\ArrayObject $values
     * @return $this
     */
    public function load($values)
    {
        if ($this->getForm()->isSubmitted() && !array_key_exists($this->getName(), $values)) {
            $this->setValue(null);
            if ($this->isArrayField()) {
                $this->setValue(array());
            }
        }
        parent::load($values);
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getOnShowOption()
    {
        return $this->onShowOption;
    }

    /**
     * Eg:
     *  function ($template, $option, $var) { }
     *
     * @param callable|null $onShowOption
     * @return Select
     */
    public function setOnShowOption($onShowOption)
    {
        $this->onShowOption = $onShowOption;
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
     * @param \Dom\Repeat $template
     * @param Option $option
     * @param string $var
     */
    private function showOption($template, $option, $var = 'option')
    {
        $template->insertText($var, $option->getName());

        $template->setAttr($var, 'value', $option->getValue());
        if ($this->isSelected($option->getValue())) {
            $template->setAttr($var, 'selected', 'selected');
        }

        if (is_callable($this->onShowOption)) {
            call_user_func_array($this->onShowOption, array($template, $option, $var));
        }

        // Add attributes
        $template->setAttr($var, $option->getAttrList());
        $template->addCss($var, $option->getCssString());
    }
    
    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();
        if (!$template->keyExists('var', 'element')) {
            return $template;
        }
        if ($this->isArrayField()) {
            $template->setAttr('element', 'multiple', 'multiple');
        }

        /* @var \Tk\Form\Field\Option $option */
        foreach($this->getOptions() as $option) {
            $tOpt = null;
            if ($option instanceof OptGroup) {
                $tOptGroup = $template->getRepeat('optgroup');
                $tOptGroup->setAttr('optgroup', 'label', $option->getName());
                foreach ($option->getOptions() as $opt) {
                    $tOpt = $tOptGroup->getRepeat('option');
                    //$this->showOption($tOptGroup, $opt);      // Wont work for some reason ??????
                    //$var = 'option';
                    $this->showOption($tOpt, $opt);
                    //$tOpt->insertText($var, $opt->getName());
                    //$tOpt->setAttr($var, 'value', $opt->getValue());
                    //if ($this->isSelected($opt->getValue())) {
                    //    $tOpt->setAttr($var, 'selected', 'selected');
                    //}
                    // Add attributes
                    //$tOpt->setAttr($var, $opt->getAttrList());
                    //$tOpt->addCss($var, $opt->getCssString());
                    $tOpt->appendRepeat();
                }
                $tOptGroup->appendRepeat();
            } else {
                /* @var \Dom\Repeat $tOpt */
                $tOpt = $template->getRepeat('option');
                $this->showOption($tOpt, $option);
                $tOpt->appendRepeat();
            }
        }

        $this->decorateElement($template);
        return $template;
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
  <optgroup label="" repeat="optgroup" var="optgroup">
    <option repeat="option" var="option"></option>
  </optgroup>
</select>
HTML;
        $tpl = \Dom\Loader::load($xhtml);
        return $tpl;
    }
}