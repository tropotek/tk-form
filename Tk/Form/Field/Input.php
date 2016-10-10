<?php
namespace Tk\Form\Field;


/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Input extends Iface
{
    
    /**
     * @var string
     */
    private $type = 'text';


    /**
     * Set the input type value
     * 
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() 
    {
        return $this->type;
    }

    /**
     * Get the field value(s).
     *
     * @return string|array
     */
    public function getValue()
    {
        return trim(parent::getValue());
    }
    
    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        $t = $this->getTemplate();
        if (!$t->keyExists('var', 'element')) {
            return $t;
        }

        // Set the input type attribute
        $t->setAttr('element', 'type', $this->getType());

        // Set the field value
        if ($t->getVarElement('element')->nodeName == 'input' ) {
            $value = $this->getValue();
            if ($value !== null && !is_array($value)) {
                $t->setAttr('element', 'value', $value);
            }
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
<input type="text" var="element" class="form-control" />
HTML;
        return \Dom\Loader::load($xhtml);
    }
    
    
    
}