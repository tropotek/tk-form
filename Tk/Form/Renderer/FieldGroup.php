<?php
namespace Tk\Form\Renderer;

use Tk\Form\Field;
use Tk\Form\Element;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class FieldGroup extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
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

        //$this->getField()->addCssClass('form-control');
        
        if ($this->getField() instanceof Field\Hidden) {
            return $this->getField()->show();
        }
        // Render the element as getHtml() triggered setting of the id attribute...
        $html = $this->getField()->show();
        if ($html instanceof \Dom\Template) {
            $t->replaceTemplate('element', $html);
        } else {
            $t->replaceHtml('element', $html);
        }


        if ($this->getField()->hasErrors()) {
            $t->addCss('field-group', 'has-error has-feedback');
            
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

        if ($this->getField()->hasShowLabel() && $this->getField()->getLabel() !== null) {
            $label = $this->getField()->getLabel();
            if ($label) $label .= ':';
            if ($this->getField()->isRequired()) {
                $t->addCss('field-group', 'required');
            }
            $t->insertHtml('label', $label);
            $t->setAttr('label', 'for', $this->getField()->getAttr('id'));
            $t->setChoice('label');
        }
        
        if ($this->getField()->getNotes() !== null) {
            $t->setChoice('notes');
            $t->insertHtml('notes', $this->getField()->getNotes());
        }
        
        $reflect = new \ReflectionClass($this->getField());
        $t->addCss('field-group',  'tk-'.strtolower( $reflect->getShortName() ));
        $t->addCss('field-group',  'tk-'.strtolower( \Tk\Dom\CssTrait::cleanCss($this->getField()->getName()) ));
        
        return $t;
    }

    /**
     * makeTemplate
     *
     * @return string
     */
    protected function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="form-group form-group-sm" var="field-group">
  <span class="help-block" choice="errorText"><span class="glyphicon glyphicon-ban-circle"></span> <span var="errorText"></span></span>
  <label class="control-label" var="label" choice="label">
  </label><div var="element" class="controls"></div>
  <span class="help-block help-text" var="notes" choice="notes">&nbsp;</span>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}
