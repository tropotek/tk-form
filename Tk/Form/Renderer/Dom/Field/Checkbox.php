<?php
namespace Tk\Form\Renderer\Dom\Field;


/**
 * Class Text
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Checkbox extends Iface
{


    /**
     * Render the field and return the template or html string
     */
    public function showElement()
    {
        $t = $this->getTemplate();
        if (!$t->keyExists('var', 'element')) {
            return;
        }
        $ret = parent::showElement();

        if ($this->getField()->isSelected()) {
            $t->setAttr('element', 'checked', 'checked');
        }
        $t->setAttr('element', 'value', $this->getField()->getName());

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
<div>
  <input type="checkbox" var="element" />
</div>
XHTML;
        return \Dom\Loader::load($xhtml);
    }

}