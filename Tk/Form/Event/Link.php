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
    public function __construct($name, $url = null, $icon = 'fa fa-times')
    {
        parent::__construct($name);
        $this->url = \Tk\Uri::create($url);
        $this->setIcon($icon);
        $this->addCss('btn btn-sm btn-default btn-once');
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
        $t = parent::show();
        
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
<a class="" var="element"><i var="iconL" choice="iconL"></i> <span var="text">Submit</span> <i var="iconR" choice="iconR"></i></a>
HTML;
        return \Dom\Loader::load($xhtml);
    }
}