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
    
    protected $html = null;

    /**
     * __construct
     *
     * @param string $name
     * @param string $html
     */
    public function __construct($name, $html = null)
    {
        parent::__construct($name);
        //$this->html = $html;
        $this->setValue($html);
    }

    /**
     * @param mixed|string $html
     * @return $this
     */
    public function setValue($html)
    {
        if ($html) {        // TODO: Check if this should be the expected behaviour
            if ($html instanceof \Dom\Template) {
                $html = $html->toString();
            }
            parent::setValue($html);
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
        $t = $this->getTemplate();
        if (!$t->keyExists('var', 'element')) {
            return $t;
        }

        $t->insertHtml('element', $this->getValue());

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
<div var="element" class="form-control-static"></div>
HTML;
        return \Dom\Loader::load($xhtml);
    }

}