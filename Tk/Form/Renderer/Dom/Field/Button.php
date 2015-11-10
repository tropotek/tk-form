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
class Button extends Iface
{

    /**
     * show()
     */
    public function show()
    {
        $t = $this->getTemplate();
        if ($t->isParsed()) return;

        $ret = $this->showElement();

        $t->insertText('text', $this->getField()->getLabel());
        if ($this->getField()->getIcon()) {
            $t->setChoice('icon');
            $t->addClass('icon', $this->getField()->getIcon());
        }
        return $ret;
    }

    /**
     * The default element template
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<XHTML
<button type="submit" class="btn btn-sm" var="element"><i var="icon" choice="icon"></i> <span var="text">Submit</span></button>
XHTML;
        return \Dom\Loader::load($xhtml);
    }

}