<?php
namespace Tk\Form\Field;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class CheckboxGroup extends Select
{
    
    /**
     * @param string $name
     * @param Option\ArrayIterator $optionIterator
     */
    public function __construct($name, Option\ArrayIterator $optionIterator = null)
    {
        parent::__construct($name);
        $this->setArrayField(true);
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
        if (!is_array($values)) {
            $values = array($this->getName() => $values);
        }
        if (!isset($values[$this->getName()])) {
            $this->values[$this->getName()] = false;
        } else {
            $this->values[$this->getName()] = $values[$this->getName()];
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

        /** @var \Tk\Form\Field\Option $option */
        foreach($this->getOptions() as $option) {
            $tOpt = $t->getRepeat('option');

            if ($option->isDisabled()) {
                $tOpt->setAttr('option', 'disabled', 'disabled');
                $tOpt->setAttr('element', 'disabled', 'disabled');
            }
            $tOpt->insertText('text', $option->getText());

            $tOpt->setAttr('element', 'value', $option->getValue());
            $tOpt->setAttr('element', 'name', $this->getName()."[]");
            
            if ($this->isSelected($option->getValue())) {
                $tOpt->setAttr('element', 'checked', 'checked');
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
    <input type="checkbox" var="element" />
    <span var="text"></span>
  </label>
</div>
</div>
XHTML;

        return \Dom\Loader::load($xhtml);
    }
    
}