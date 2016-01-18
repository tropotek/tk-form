<?php
namespace Tk\Form\Renderer;

use Tk\Form\Field;
use Tk\Form\Event;
use Tk\Form\Element;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class FieldGroup extends \Dom\Renderer\Renderer
{

    /**
     * @var Element
     */
    protected $field = null;


    /**
     * __construct
     *
     *
     * @param Field\Iface $field
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * @param $field
     * @return FieldGroup
     */
    static function create($field)
    {
        return new static($field);
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
     * Render
     */
    public function show()
    {
        $t = $this->getTemplate();
        $this->getField()->addCss('form-control');
        
        if ($this->getField() instanceof Field\Hidden) {
            return $this->getField()->getHtml();
        }
        
        if ($this->getField()->hasErrors()) {
            $t->addClass('field-group', 'has-error');
            
            $estr = '';
            foreach ($this->getField()->getErrors() as $error) {
                if ($error)
                    $estr = $error . "<br/>\n";
            }
            if ($estr) {
                $estr = substr($estr, 0, -6);
                $t->insertHtml('errorText', $estr);
                $t->setChoice('errorText');
            }
        }

        if ($this->getField()->getLabel()) {
            $label = $this->getField()->getLabel();
            if ($this->getField()->isRequired()) $label .= ' <em>*</em>';
            $t->insertHtml('label', $label);
            $t->setAttr('label', 'for', $this->getField()->getAttr('id'));
            $t->setChoice('label');
        }
        
        if ($this->getField()->getNotes()) {
            $t->setChoice('notes');
            $t->insertHtml('notes', $this->getField()->getNotes());
        }
        
        $html = $this->getField()->getHtml();
        if ($html instanceof \Dom\Template) {
            $t->appendTemplate('element', $html);
        } else {
            $t->appendHtml('element', $html);
        }
        
        
        return $t;
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
  <div var="element" class="controls"></div>
  <span class="help-block help-text" var="notes" choice="notes"></span>
</div>
XHTML;

        return \Dom\Loader::load($xhtml);
    }

}
