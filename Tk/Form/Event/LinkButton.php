<?php
namespace Tk\Form\Event;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class LinkButton extends Link
{
    /**
     * @var string|\Tk\Uri
     */
    protected $url = null;


    /**
     * __construct
     *
     * @param string $name
     * @param string|\Tk\Uri $url
     * @param string $icon
     * @throws \Tk\Form\Exception
     */
    public function __construct($name, $url, $icon = '')
    {
        if (!$icon) {
            if ($name == 'cancel') {
                $icon = 'glyphicon glyphicon-remove';
            }
        }
        parent::__construct($name, null, $icon);
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
    public function show()
    {
        $t = $this->getTemplate();
        
        if ($t->isParsed()) return '';

        if (!$t->keyExists('var', 'element')) {
            return '';
        }

        // Field name attribute
        //$t->setAttr('element', 'type', $this->getType());
        //$t->setAttr('element', 'name', $this->getName());
        $t->setAttr('element', 'name', $this->getEventName());

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

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<a class="btn btn-sm btn-default" var="element"><i var="icon" choice="icon"></i> <span var="text">Link</span></a>
HTML;
        return \Dom\Loader::load($xhtml);
    }
}