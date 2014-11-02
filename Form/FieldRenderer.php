<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form;

/**
 * A base object for all forms and its elements
 *
 *
 * @package Form
 */
class FieldRenderer extends \Mod\Renderer
{

    /**
     * @var Field\Iface
     */
    protected $field = null;




    /**
     * __construct
     *
     *
     * @param Field\Iface $field
     */
    public function __construct(Field\Iface $field)
    {
        $this->field = $field;
        $this->setInstanceId($field->getInstanceId());

        $this->show();
    }

    /**
     * Create a new Form Renderer.
     *
     * @param Field\Iface $field
     * @return FieldRenderer
     */
    static function create(Field\Iface $field)
    {
        return new self($field);
    }


    /**
     * Get the form object
     *
     * @return Field\Iface
     */
    public function getField()
    {
        return $this->field;
    }



    /**
     * Render
     *
     */
    public function show()
    {
        $t = $this->getTemplate();
        $this->field->show();

        if (!$this->field->hasFieldWrapper()) {
            $this->setTemplate($this->field->getTemplate());
            return;
        }

        if (in_array($this->field->getForm()->getCssClassList(), 'form-horizontal')) {
            $t->addClass('label', 'control-label');
        }

        $t->addClass('field', $this->field->getClassName(true) );
        $t->addClass('field', 'tk-field-'.$this->field->getName());

        if (count($this->field->getFieldClassList())) {
            foreach ($this->field->getFieldClassList() as $k => $v) {
                $t->addClass('field', $v);
            }
        }

        if ($this->field->hasErrors()) {
            $t->setChoice('error');
            $t->insertHtml('error', $this->field->getErrrorHtml());
            $t->addClass('field', 'has-error');
        }

        if ($this->field->getLabel()) {
            $label = $this->field->getLabel();
            if ($this->field->isRequired()) $label .= ' <em>*</em>';
            $t->insertHtml('label', $label);
            $t->setAttr('label', 'for', $this->field->getId());
            $t->setChoice('label');
        } else {
            //$t->addClass('controls', 'col-sm-offset-2');
        }

        if ($this->field->getNotes()) {
            $t->setChoice('notes');
            $t->insertHtml('notes', $this->field->getNotes());
        }

        $t->replaceTemplate('element', $this->field->getTemplate());
    }


    /**
     * makeTemplate
     *
     * @return string
     */
    protected function __makeTemplate()
    {

        $xmlStr = <<<XML
<?xml version="1.0"?>
<div class="form-group" var="field">
  <label class="control-label" var="label" choice="label"></label>
  <div class="controls" var="controls">
    <div class="error error-text clearfix" choice="error"><span class="glyphicon glyphicon-ban-circle"></span> <div class="err" var="error"></div></div>
    <div var="element"></div>
    <span class="help-block" var="notes" choice="notes"></span>
  </div>
  
</div>
XML;

        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }


}
