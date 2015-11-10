<?php
namespace Tk\Form\Renderer\Dom\Field;

use Tk\Form\Field;

/**
 * Class Text
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Link extends Iface
{

    /**
     * show()
     */
    public function show()
    {
        $t = $this->getTemplate();
        if ($t->isParsed()) return;

        $this->showElement();

        $t->insertText('text', $this->getField()->getLabel());
        $t->setAttr('element', 'href', $this->getField()->getUrl());
        if ($this->getField()->getIcon()) {
            $t->setChoice('icon');
            $t->addClass('icon', $this->getField()->getIcon());
        }
    }

    /**
     * The default element template
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<XHTML
<a class="btn btn-sm" var="element"><i var="icon" choice="icon"></i> <span var="text">Link</span></a>
XHTML;
        return \Dom\Loader::load($xhtml);
    }

}