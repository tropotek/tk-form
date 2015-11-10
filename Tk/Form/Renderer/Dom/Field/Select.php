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
class Select extends Iface
{


    /**
     *
     * @return $this
     */
    public function showElement()
    {
        $t = $this->getTemplate();
        /** @var \Tk\Form\Field\Option $option */
        foreach($this->getField()->getOptions() as $option) {
            $tOpt = $t->getRepeat('option');

            if ($option->isDisabled()) {
                $tOpt->setAttr('option', 'disabled', 'disabled');
            }
            if ($option->getLabel()) {
                $tOpt->setAttr('option', 'label', $option->getLabel());
            }
            // TODO: render optgroup

            $tOpt->setAttr('option', 'value', $option->getValue());
            if ($this->getField()->isSelected($option->getValue())) {
                $tOpt->setAttr('option', 'selected', 'selected');
            }
            $tOpt->insertText('option', $option->getText());
            $tOpt->appendRepeat();
        }
        if ($this->getField()->isArray()) {
            $t->setAttr('element', 'multiple', 'multiple');
        }

        return parent::showElement();
    }



    /**
     * The default element template
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<XHTML
<select class="form-control" var="element">
  <option value="" repeat="option" var="option"></option>
</select>
XHTML;
        return \Dom\Loader::load($xhtml);
    }

}