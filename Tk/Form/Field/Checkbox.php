<?php
namespace Tk\Form\Field;

/**
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
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        $this->removeCss('form-control');
        $t = parent::getHtml();
        
        if ($this->getValue()) {
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
        $xhtml = <<<XHTML
<div class="checkbox">
  <label>
    <input type="checkbox" var="element"/> <span var="label"></span>
  </label>
</div>
XHTML;
        return \Dom\Loader::load($xhtml);
    }
    
}