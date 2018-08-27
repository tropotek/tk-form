<?php
namespace Tk\Form\Renderer;

use function PHPSTORM_META\type;
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
        $template = clone $this->getTemplate();     // Get a fresh template for each field

        $this->showErrors($template);
        $this->showLabel($template);
        $this->showNotes($template);
        $this->showField($template);

        // Form Group Styles
        $reflect = new \ReflectionClass($this->getField());
        $template->addCss('form-group',  'tk-'.strtolower( $reflect->getShortName() ));
        $template->addCss('form-group',  'tk-'.strtolower( \Tk\Dom\CssTrait::cleanCss($this->getField()->getName()) ));

        if ($this->getOnShow()) {
            call_user_func_array($this->getOnShow(), array($template, $this->getField()));
        }

        return $template;
    }

    /**
     * @param \Dom\Template $template
     */
    protected function showField($template)
    {
        $html = $this->getField()->getTemplate();
        if ($html instanceof \Dom\Template) {
            $template->replaceTemplate('element', $html);
        } else {
            $template->replaceHtml('element', $html);
        }
    }


    /**
     * @param \Dom\Template $template
     */
    protected function showErrors($template)
    {
        if ($this->getField()->hasErrors()) {
            $template->addCss('form-group', 'has-error is-invalid has-feedback');
            $estr = '';
            foreach ($this->getField()->getErrors() as $error) {
                if ($error)
                    $estr .= $error . "<br/>\n";
            }
            if ($estr) {
                $estr = substr($estr, 0, -6);
                $template->appendHtml('errorText', $estr);
                $template->setChoice('errorText');
            }
        }
    }

    /**
     * @param \Dom\Template $template
     */
    protected function showLabel($template)
    {
        if ($this->getField()->hasShowLabel() && $this->getField()->getLabel() !== null) {
            $label = $this->getField()->getLabel();
            if ($label) $label .= ':';
            if ($this->getField()->isRequired()) {
                $template->addCss('form-group', 'required');
                $template->setAttr('label', 'title', 'Required');
            }
            $template->appendHtml('label', $label);
            $template->setAttr('label', 'for', $this->getField()->getAttr('id'));
            $template->setChoice('label');
        }
    }

    /**
     * @param \Dom\Template $template
     */
    protected function showNotes($template)
    {
        if ($this->getField()->getNotes() !== null) {
            $template->setChoice('notes');
            $template->appendHtml('notes', $this->getField()->getNotes());
        }
    }

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    protected function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="form-group form-group-sm" var="form-group">
  <label class="control-label" var="label" choice="label"></label>
  <span class="help-block error-block"><span class="" var="errorText" choice="errorText"></span></span>
  <div var="element" class="controls"></div>
  <span class="help-block help-text" var="notes" choice="notes">&nbsp;</span>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}
