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
     * @var bool
     */
    protected $escapeText = false;


    /**
     * __construct
     *
     * @param string $name
     * @param null|string|\Dom\Template $html
     */
    public function __construct($name, $html = null)
    {
        parent::__construct($name);
        $this->setHtml($html);
        $this->addCss('');
        $this->setReadonly(true);
    }

    /**
     * @param string $name
     * @param null|string $html
     * @return Html
     */
    public static function createHtml($name, $html = null)
    {
        $obj = new static($name, $html);
        return $obj;
    }

    /**
     * @param bool $b
     * @return $this
     */
    public function setEscapeText($b = true)
    {
        $this->escapeText = $b;
        return $this;
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
     */
    public function show()
    {
        $template = $this->getTemplate();
        if (!$template->keyExists('var', 'element')) {
            return $template;
        }

        $html = $this->getValue();

        if ($this->html !== null)
            $html = $this->html;

        if ($html instanceof \Dom\Template) {
            $template->appendTemplate('element', $html);
        } else {
            if ($this->escapeText) {
                $html = htmlentities($html);
            }
            $template->appendHtml('element', $html);
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
<div var="element" class=""></div>
HTML;
        return \Dom\Loader::load($xhtml);
    }

}