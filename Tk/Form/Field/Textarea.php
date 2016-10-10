<?php
namespace Tk\Form\Field;


/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Textarea extends Iface
{
    
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

        // set the field value
        if ($t->getVarElement('element')->nodeName == 'textarea') {
            $value = $this->getValue();
            if ($value !== null && !is_array($value)) {
                $t->insertText('element', $value);
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
<textarea var="element" class="form-control" rows="3"></textarea>
HTML;
        return \Dom\Loader::load($xhtml);
    }
    
    
}