<?php
namespace Tk\Form\Field;


/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Html extends Input
{
    /**
     * @var null|string|\Dom\Template
     */
    protected $html = null;


    /**
     * __construct
     *
     * @param string $name
     * @param null|string|\Dom\Template $html
     * @throws \Tk\Form\Exception
     */
    public function __construct($name, $html = null)
    {
        parent::__construct($name);
        $this->setHtml($html);
        $this->addCss('form-control-static');
    }

    /**
     * @param null|string|\Dom\Template $html
     * @return $this
     */
    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     * @throws \Dom\Exception
     */
    public function show()
    {
        $t = $this->getTemplate();
        if (!$t->keyExists('var', 'element')) {
            return $t;
        }

        $html = $this->getValue();

        if ($this->html !== null)
            $html = $this->html;

        if ($html instanceof \Dom\Template) {
            $t->insertTemplate('element', $html);
        } else {
            $t->insertHtml('element', $html);
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
<div var="element" class=""></div>
HTML;
        return \Dom\Loader::load($xhtml);
    }

}