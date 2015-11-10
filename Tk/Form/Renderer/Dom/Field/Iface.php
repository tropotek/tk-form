<?php
namespace Tk\Form\Renderer\Dom\Field;

use Tk\Form\Field;

/**
 * Field DOM renderer interface
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
abstract class Iface extends \Dom\Renderer\Renderer
{
    /**
     * @var Field\Iface
     */
    protected $field = null;


    /**
     * __construct
     *
     * @param Field\Iface $field
     */
    public function __construct(Field\Iface $field)
    {
        $this->field = $field;
    }

    /**
     *
     * @return Field\Iface
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Render the field
     */
    public function show()
    {
        $t = $this->getTemplate();
        if ($t->isParsed()) return;

        $this->showElement();

        return $this;
    }

    /**
     * Render the field and return the template or html string
     */
    public function showElement()
    {
        $t = $this->getTemplate();
        if (!$t->keyExists('var', 'element')) {
            return;
        }

        // Field name attribute
        $t->setAttr('element', 'name', $this->getField()->getName());

        // All other attributes
        foreach($this->getField()->getAttrList() as $key => $val) {
            if ($val == '' || $val == null) {
                $val = $key;
            }
            $t->setAttr('element', $key, $val);
        }

        // Element css class names
        foreach($this->getField()->getCssList() as $v) {
            $t->addClass('element', $v);
        }

        if ($this->getField()->isRequired()) {
            $t->setAttr('element', 'required', 'required');
        }

        // the current field value
        //$valid = array('text', 'date', 'time');
        //if ($t->getVarElement('element')->nodeName == 'input' && in_array($t->getVarElement('element')->getAttribute('type'), $valid) ) {
        if ($t->getVarElement('element')->nodeName == 'input' ) {
            // Render a value
            $fieldValues = $this->getField()->getForm()->getStringValues();
            if (isset($fieldValues[$this->getField()->getName()]) && !is_array($fieldValues[$this->getField()->getName()])) {
                $t->setAttr('element', 'value', $fieldValues[$this->getField()->getName()]);
            }
        }
    }
}