<?php
namespace Tk\Form\Event;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Link extends Button
{
    /**
     * @var string|\Tk\Url
     */
    protected $url = null;


    /**
     * __construct
     *
     * @param string $name
     * @param string|\Tk\Url $url
     * @param callable $callback
     */
    public function __construct($name, $url = null, $callback = null, $icon = '')
    {
        if (!$icon) {
            if ($name == 'cancel') {
                $icon = 'glyphicon glyphicon-remove';
            }
        }
        parent::__construct($name, $callback, $icon);
        
        if (!$url) {
            $url = \Tk\Url::create();
        }
        if ($callback) { // required to execute the callback
            $url->set($name, $name);
        }
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        $xhtml = <<<XHTML
<a class="btn btn-sm btn-default" var="element"><i var="icon" choice="icon"></i> <span var="text">Link</span></a>
XHTML;
        $t = \Dom\Loader::load($xhtml);
        
        if ($t->isParsed()) return '';

        if (!$t->keyExists('var', 'element')) {
            return '';
        }

        // Field name attribute
        //$t->setAttr('element', 'type', $this->getType());
        $t->setAttr('element', 'name', $this->getName());

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

        $t->insertText('text', $this->getLabel());
        if ($this->getIcon()) {
            $t->setChoice('icon');
            $t->addClass('icon', $this->getIcon());
        }
        
        $t->setAttr('element', 'href', $this->getUrl());
        
        return $t;
    }
}