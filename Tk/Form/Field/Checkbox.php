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
        // TODO: We have a major real-time update value issue here.
        // 1. when we set the form values from the request, !isset($values[$name])
        //    means that the checkbox is to be unset
        // 2. when we set the form values form an array with a select data set of
        //    field values then the checkbox is unset but we intended that it be ignored
        //
        // Solutions:
        //
        // 1. We could add all values to an internal array so multiple calls to $form->load();
        //    are not executed individually but only once the $form->execute method is called
        //    are the actual field values set but using all data in the temp array??????
        //    So multiple calls to load() will override previous values but this should
        //    deliver a consistent result
        //
        //
        //
//vd($values);


        if (!is_array($values)) {
            $values = array($this->getName() => $values);
        }
        if (!isset($values[$this->getName()])) {
            $this->values[$this->getName()] = '';
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