<?php
namespace Tk\Form\Field;

/**
 *
 *
 * @bug: little bug when fields are disabled, the checkbox state is assumed false instead of ignored? Not sure the fix here.
 *
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Checkbox extends Input
{

    /**
     * @var string
     */
    private $checkboxLabel = '';


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

    /**
     * Set the label of this field
     *
     * @param $str
     * @return Checkbox|Input
     */
    public function setLabel($str)
    {
        return parent::setLabel($str);
    }

    /**
     * @return string
     */
    public function getCheckboxLabel()
    {
        return $this->checkboxLabel;
    }

    /**
     * @param string $checkboxLabel
     * @return $this
     */
    public function setCheckboxLabel($checkboxLabel)
    {
        //$this->setLabel('');
        $this->checkboxLabel = $checkboxLabel;
        return $this;
    }

    public function load($values)
    {
        parent::load($values);
        if (!isset($values[$this->getName()]) && $this->getValue() === null) {
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
    public function show()
    {
        $template = parent::show();
        if ($this->getValue() !== null && ($this->getValue() == $this->getName() || $this->getValue() === true)) {
            $template->setAttr('element', 'checked', 'checked');
        }
        $template->setAttr('element', 'value', $this->getName());
        $template->setAttr('hidden', 'name', $this->getName());
        $template->setAttr('hidden', 'value', '');
        $template->setAttr('checkbox-label', 'for', $this->getId());
        if ($this->getCheckboxLabel()) {
            $template->insertHtml('label', $this->getCheckboxLabel());
            $template->addCss('checkbox', 'is-cbl');
        }
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
<div class="checkbox" var="checkbox">
  <label var="checkbox-label">
    <input type="hidden" var="hidden" value=""/>
    <input type="checkbox" var="element"/> 
    <span var="label" class="cb-label">&nbsp;</span>
  </label>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }
    
}