<?php
namespace Tk\Form\Field;


/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Textarea extends Iface
{
    
    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();
        if (!$template->keyExists('var', 'element')) {
            return $template;
        }

        // set the field value
        if ($template->getVarElement('element')->nodeName == 'textarea') {
            $value = $this->getValue();
            if ($value !== null && !is_array($value)) {
                $template->insertText('element', $value);
            }
        }

        $this->decorateElement($template);
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
<textarea class="form-control" var="element"></textarea>
HTML;
        return \Dom\Loader::load($xhtml);
    }
    
    
}