<?php
namespace Tk\Form\Field;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class RadioGroup extends Select
{
    
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
     * Set the field value(s)
     *
     * @param array|string $values
     * @return $this
     */
    public function setValue($values)
    {
        vd($values);
        if (!is_array($values)) {
            $values = array($this->getName() => $values);
        }
        if (!isset($values[$this->getName()])) return $this;
   
        $this->values[$this->getName()] = $values[$this->getName()];

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
        //vd($value, $val);
        if ($value && $value == $val) {
            return true;
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
        $t = $this->__makeTemplate();
        $c = false;
        /** @var \Tk\Form\Field\Option $option */
        foreach($this->getOptions() as $option) {
            $tOpt = $t->getRepeat('option');

            if ($option->isDisabled()) {
                $tOpt->setAttr('option', 'disabled', 'disabled');
                $tOpt->setAttr('element', 'disabled', 'disabled');
            }
            $tOpt->insertText('text', $option->getText());

            $tOpt->setAttr('element', 'value', $option->getValue());
            $tOpt->setAttr('element', 'name', $this->getName());
            
            // allow only one radio to be selected.
            if ($this->getValue() == $option->getValue() && !$c) {
                $tOpt->setAttr('element', 'checked', 'checked');
                $c = true;
            }

            // All other attributes
            foreach($this->getAttrList() as $key => $val) {
                if ($val == '' || $val == null) {
                    $val = $key;
                }
                $t->setAttr('element', $key, $val);
            }

            // Element css class names
            foreach($this->getCssList() as $v) {
                $t->addClass('element', $v);
            }
            
            $tOpt->appendRepeat();
        }
        return $t;
    }

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<XHTML
<div>
<div class="checkbox" repeat="option" var="option">
  <label var="label">
    <input type="radio" var="element" />
    <span var="text"></span>
  </label>
</div>
</div>
XHTML;

        return \Dom\Loader::load($xhtml);
    }
    
}