<?php
namespace Tk\Form\Renderer\Dom;


/**
 * Class FieldRenderer
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class FieldGroup extends \Dom\Renderer\Renderer
{

    /**
     * @var Field\Iface
     */
    protected $fieldRenderer = null;


    /**
     * __construct
     *
     *
     * @param Field\Iface $fieldRenderer
     */
    public function __construct($fieldRenderer)
    {
        $this->fieldRenderer = $fieldRenderer;
    }

    /**
     * @param $fieldRenderer
     * @return FieldGroup
     */
    static function create($fieldRenderer)
    {
        return new static($fieldRenderer);
    }

    /**
     * 
     * @return Field\Iface
     */
    public function getFieldRenderer()
    {
        return $this->fieldRenderer;
    }

    /**
     * Render
     */
    public function show()
    {
        $t = $this->getTemplate();
        $this->getFieldRenderer()->show();

        if ($this->getFieldRenderer()->getField()->hasErrors()) {
            $t->addClass('field-group', 'has-error');
            
            $estr = '';
            foreach ($this->getFieldRenderer()->getField()->getErrors() as $error) {
                if ($error)
                    $estr = $error . "<br/>\n";
            }
            if ($estr) {
                $estr = substr($estr, 0, -6);
                $t->insertHtml('errorText', $estr);
                $t->setChoice('errorText');
            }
        }

        if ($this->getFieldRenderer()->getField()->getLabel()) {
            $label = $this->getFieldRenderer()->getField()->getLabel();
            if ($this->getFieldRenderer()->getField()->isRequired()) $label .= ' <em>*</em>';
            $t->insertHtml('label', $label);
            $t->setAttr('label', 'for', $this->getFieldRenderer()->getField()->getAttr('id'));
            $t->setChoice('label');
        }

        if ($this->getFieldRenderer()->getField()->getNotes()) {
            $t->setChoice('notes');
            $t->insertHtml('notes', $this->getFieldRenderer()->getField()->getNotes());
        }

        $t->replaceTemplate('element', $this->getFieldRenderer()->getTemplate());
        $this->getFieldRenderer()->setTemplate($this->getTemplate());
        return $this;
    }

    /**
     * makeTemplate
     *
     * @return string
     */
    protected function __makeTemplate()
    {
        $xhtml = <<<XHTML
<div class="form-group form-group-sm " var="field-group">
  <label class="control-label" var="label" choice="label"></label>
  <span class="help-block error-text" choice="errorText"><span class="glyphicon glyphicon-ban-circle"></span> <span var="errorText"></span></span>
  <div var="element"></div>
  <span class="help-block help-text" var="notes" choice="notes"></span>
</div>
XHTML;

        return \Dom\Loader::load($xhtml);
    }

}
