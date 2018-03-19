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
     * @throws \Tk\Form\Exception
     */
    public function __construct($name, $optionIterator = null)
    {
        parent::__construct($name, $optionIterator);
        $this->setArrayField(true);
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
    public function show()
    {
        $t = $this->getTemplate();

        /* @var \Tk\Form\Field\Option $option */
        foreach($this->getOptions() as $option) {
            $tOpt = $t->getRepeat('option');

            if ($option->isDisabled()) {
                $tOpt->setAttr('option', 'disabled', 'disabled');
                $tOpt->setAttr('element', 'disabled', 'disabled');
            }
            $tOpt->insertText('text', $option->getText());

            $tOpt->setAttr('element', 'value', $option->getValue());
            $tOpt->setAttr('element', 'name', $this->getFieldName());
            
            if ($this->isSelected($option->getValue())) {
                $tOpt->setAttr('element', 'checked', 'checked');
            }

            // All other attributes
            foreach($this->getAttrList() as $key => $val) {
                if ($val == '' || $val == null) {
                    $val = $key;
                }
                $tOpt->setAttr('element', $key, $val);
            }

            // Element css class names
            foreach($this->getCssList() as $v) {
                $tOpt->addCss('element', $v);
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
<div var="group">
  <div class="checkbox" repeat="option" var="option">
    <label var="label">
      <input type="checkbox" var="element" />
      <span var="text"></span>
    </label>
  </div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }
    
}