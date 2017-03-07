<?php
namespace Tk\Form\Field;

/**
 *
 *
 * @bug: little bug when fields are disabled, the checkbox state is assumed false instead of ignored? Not sure the fix here.
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Checkbox extends Input
{
    
    /**
     * __construct
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setType('checkbox');
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

    public function setValue($value)
    {
        if ($value === true) {
            $value = $this->getName();
        } elseif ($value === false) {
            $value = '';
        }
        $this->value = $value;
        return $this;
    }
    
    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        $t = parent::getHtml();
        //if ($this->getValue() !== null) {
        if ($this->getValue() !== null && ($this->getValue() == $this->getName() || $this->getValue() === true)) {
            $t->setAttr('element', 'checked', 'checked');
        }
        $t->setAttr('element', 'value', $this->getName());
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
<div class="checkbox">
  <label>
    <input type="checkbox" var="element"/> <span var="label"></span>
  </label>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }
    
}