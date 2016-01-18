<?php
namespace Tk\Form\Field;


/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Html extends Input
{
    
    protected $html = '';

    /**
     * __construct
     *
     * @param string $name
     * @param string $html
     */
    public function __construct($name, $html = '')
    {
        parent::__construct($name);
        $this->html = $html;
    }

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        $t = $this->__makeTemplate();
        $this->removeCss('form-control');

        if (!$t->keyExists('var', 'element')) {
            return '';
        }

        // Field name attribute
        if ($this->html instanceof \Dom\Template) {
            $t->insertTemplate('element', $this->html);
        } else {
            $t->insertHtml('element', $this->html);
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
<div var="element"></div>
XHTML;
        return \Dom\Loader::load($xhtml);
    }

}