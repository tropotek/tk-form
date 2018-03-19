<?php
namespace Tk\Form\Field;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Radio extends Select
{

    /**
     * @var null|callable
     */
    protected $onShowOption = null;


    /**
     * @param string $name
     * @param Option\ArrayIterator $optionIterator
     * @throws \Tk\Form\Exception
     */
    public function __construct($name, Option\ArrayIterator $optionIterator = null)
    {
        parent::__construct($name);
        if ($optionIterator) {
            $this->appendOptionIterator($optionIterator);
        }
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
        if ($value !== null && $value == $val) {
            return true;
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

        $checkedSet = false;
        /* @var \Tk\Form\Field\Option $option */
        foreach($this->getOptions() as $option) {
            $tOpt = $t->getRepeat('option');

            if (!$tOpt->keyExists('var', 'element')) continue;

            if ($option->isDisabled()) {
                $tOpt->setAttr('option', 'disabled', 'disabled');
                $tOpt->setAttr('element', 'disabled', 'disabled');
            }
            $tOpt->insertText('text', $option->getText());

            $tOpt->setAttr('element', 'value', $option->getValue());
            $tOpt->setAttr('element', 'name', $this->getFieldName());
            
            // allow only one radio to be selected.
            if ($this->isSelected($option->getValue()) && !$checkedSet) {
                $tOpt->setAttr('element', 'checked', 'checked');
            }

            // All other attributes
            $t->setAttr('element', $this->getAttrList());

            // Element css class names
            $t->addCss('element', $this->getCssString());

            if (is_callable($this->onShowOption)) {
                call_user_func_array($this->onShowOption, array($tOpt, $option, $checkedSet));
            }

            if ($this->getValue() == $option->getValue() && !$checkedSet) {
                $checkedSet = true;
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
<div>
  <div class="radio" repeat="option" var="option">
    <label var="label">
      <input type="radio" var="element" />
      <span var="text"></span>
    </label>
  </div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }
    
}