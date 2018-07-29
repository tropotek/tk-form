<?php
namespace Tk\Form\Renderer;

use Tk\Form\Field;
use Tk\Form\Element;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class FieldGroup extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{

    /**
     * @var \Tk\Form
     */
    protected $form = null;

    /**
     * @var Field\Iface
     */
    protected $field = null;

    /**
     * @var null|callable
     */
    protected $onShow = null;


    /**
     * @param \Tk\Form $form
     */
    public function __construct($form)
    {
        $this->form = $form;
    }

    /**
     * @param \Tk\Form $form
     * @return FieldGroup|static
     */
    static function create($form)
    {
        $obj = new static($form);
        return $obj;
    }

    /**
     * @return \Tk\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param \Tk\Form $form
     * @return static
     */
    public function setForm(\Tk\Form $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return Field\Iface
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param Field\Iface $field
     * @return $this
     */
    public function setField(Field\Iface $field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return callable|null
     */
    protected function getOnShow()
    {
        return $this->onShow;
    }

    /**
     * @param callable|null $onShow
     * @return $this
     */
    public function setOnShow(callable $onShow)
    {
        $this->onShow = $onShow;
        return $this;
    }

    /**
     * @return \Dom\Renderer\Renderer|\Dom\Template|null
     * @throws \Exception
     */
    public function show()
    {
        $this->template = $this->__makeTemplate();
        $t = $this->getTemplate();

        $html = $this->getField()->getTemplate();
        if ($html instanceof \Dom\Template) {
            $t->replaceTemplate('element', $html);
        } else {
            $t->replaceHtml('element', $html);
        }

        if ($this->getField()->hasErrors()) {
            $t->addCss('field-group', 'has-error is-invalid has-feedback');
            
            $estr = '';
            foreach ($this->getField()->getErrors() as $error) {
                if ($error)
                    $estr .= $error . "<br/>\n";
            }
            if ($estr) {
                $estr = substr($estr, 0, -6);
                $t->appendHtml('errorText', $estr);
                $t->setChoice('errorText');
            }
        }

        if ($this->getField()->hasShowLabel() && $this->getField()->getLabel() !== null) {
            $label = $this->getField()->getLabel();
            if ($label) $label .= ':';
            if ($this->getField()->isRequired()) {
                $t->addCss('field-group', 'required');
                $t->setAttr('label', 'title', 'Required');
            }
            $t->appendHtml('label', $label);
            $t->setAttr('label', 'for', $this->getField()->getAttr('id'));
            $t->setChoice('label');
        }
        
        if ($this->getField()->getNotes() !== null) {
            $t->setChoice('notes');
            $t->appendHtml('notes', $this->getField()->getNotes());
        }
        
        $reflect = new \ReflectionClass($this->getField());
        $t->addCss('field-group',  'tk-'.strtolower( $reflect->getShortName() ));
        $t->addCss('field-group',  'tk-'.strtolower( \Tk\Dom\CssTrait::cleanCss($this->getField()->getName()) ));

        if ($this->getOnShow()) {
            call_user_func_array($this->getOnShow(), array($t, $this->getField()));
        }

        return $t;
    }

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    protected function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="form-group form-group-sm" var="field-group">
  <label class="control-label" var="label" choice="label"></label>
  <span class="help-block error-block"><span class="" var="errorText" choice="errorText"></span></span>
  <div var="element" class="controls"></div>
  <span class="help-block help-text" var="notes" choice="notes">&nbsp;</span>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}
