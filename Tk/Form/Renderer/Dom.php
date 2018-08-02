<?php
namespace Tk\Form\Renderer;

use Dom\Template;
use Tk\Form\Field;
use Tk\Form\Event;
use Tk\Form;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Dom extends Iface
{
//
//    /**
//     * @deprecated Use setFieldGroupRenderer
//     */
//    const DEFAULT_FIELD_TEMPLATE = '\Tk\Form\Renderer\FieldGroup';
//
//    /**
//     * @var string
//     * @deprecated Use setFieldGroupRenderer
//     */
//    protected $fieldGroupClass = '';

    /**
     * @var null|\Tk\Form\Renderer\FieldGroup
     */
    protected $fieldGroupRenderer = null;


    /**
     * @param Form $form
     * @return Dom
     */
    static function create($form)
    {
        $obj = new static($form);

        // TODO: remove this once we have fixed external dependant codes
        $obj->setFieldGroupRenderer(FieldGroup::create($form));

        return $obj;
    }

    /**
     * @param string $fieldGroupClass
     * @return $this
     * @deprecated Use setFieldGroupRenderer
     */
    public function setFieldGroupClass($fieldGroupClass)
    {
        \Tk\Log::notice('Using deprecated function: \Tk\Form\Renderer\Dom::setFieldGroupClass()');
        return $this;
    }

    /**
     * @return null|FieldGroup
     */
    public function getFieldGroupRenderer()
    {
        return $this->fieldGroupRenderer;
    }

    /**
     * @param null|FieldGroup $fieldGroupRenderer
     * @return static
     */
    public function setFieldGroupRenderer($fieldGroupRenderer)
    {
        $this->fieldGroupRenderer = $fieldGroupRenderer;
        return $this;
    }

    /**
     * Render the field and return the template or html string
     *
     * @return \Dom\Template
     * @throws \Exception
     * @todo This should return the Template object as per all other Renderer interfaces....
     * @todo this will affect all projects, needs to be done ASAP, before EMS release.
     */
    public function show()
    {
        if ($this->getForm()->getDispatcher()) {
            $e = new \Tk\Event\FormEvent($this->getForm());
            $e->set('form', $this->getForm());
            $this->getForm()->getDispatcher()->dispatch(\Tk\Form\FormEvents::FORM_SHOW, $e);
        }

        $template = $this->getTemplate();
        if (!$template->keyExists('var', 'form')) {
            return $template;
        }

        // Field name attribute
        $template->setAttr('form', 'id', $this->getForm()->getId());

        // All other attributes
        $template->setAttr('form' ,$this->getForm()->getAttrList());

        // Element css class names
        $template->addCss('form', $this->getForm()->getCssList());

        // render form errors
        if ($this->getForm()->hasErrors()) {
            /* @var $field Field\Iface */
            foreach ($this->getForm()->getFieldList() as $field) {
                if ($field->hasErrors()) {
                    $field->addCss('errors');
                }
            }
            $estr = '';
            foreach ($this->getForm()->getErrors() as $error) {
                if ($error) {
                    $estr .= '<div class="field-error">' . htmlentities($error) . "</div>\n";
                }
            }
            if ($estr) {
                $template->appendHtml('errors', $estr);
                $template->setChoice('errors');
            }
        }

        $this->showFields($template);

        return $template;
    }

    /**
     * Render Fields
     *
     * @param Template $t
     * @throws \Exception
     */
    public function showFields(Template $t)
    {
        $i = 0;
        $tabGroups = array();

        $fieldList = $this->groupFieldset($this->form->getFieldList());
        $fieldsetName = 'null';
        /** @var \Dom\Repeat $setRow */
        $setRow = null;
        /* @var $field Field\Iface */
        foreach ($fieldList as $field) {
            if (!$field->getTabGroup()) {
                if (!$field->getFieldset()) {
                    $this->showField($field, $t, 'fields');
                } else {
                    if ($fieldsetName != $field->getFieldset()) {
                        if ($setRow) {
                            $setRow->appendRepeat('fields');
                        }
                        $setRow = $t->getRepeat('fieldset');
                        $setRow->insertText('legend', $field->getFieldset());
                        $setRow->addCss('fieldset', preg_replace('/[^a-z0-9_-]/i', '', $field->getFieldset()) );
                    }
                    $this->showField($field, $setRow, 'fieldset');
                }

                $fieldsetName = $field->getFieldset();
                $i++;
            } else {
                if (!isset($tabGroups[$field->getTabGroup()])) {
                    $tabGroups[$field->getTabGroup()] = array();
                }
                $tabGroups[$field->getTabGroup()][] = $field;
            }
        }
        if ($setRow) {
            $setRow->appendRepeat('fields');
        }

        $fieldsetName = 'null';
        /** @var \Dom\Repeat $setRow */
        $setRow = null;
        $i = (count($tabGroups)%2) ? 0 : 1;
        foreach ($tabGroups as $gname => $group) {
            $tabBox = $t->getRepeat('tabBox');
            foreach ($group as $field) {
                $tabBox->setAttr('tabBox', 'id', $this->form->getId().$this->cleanName($gname));
                $tabBox->setAttr('tabBox', 'data-name', $gname);
                if (!$field->getFieldset()) {
                    $this->showField($field, $tabBox, 'tabBox');
                } else {
                    if ($fieldsetName != $field->getFieldset()) {
                        if ($setRow) {
                            $setRow->appendRepeat('tabBox');
                        }
                        $setRow = $tabBox->getRepeat('fieldset');
                        $setRow->insertText('legend', $field->getFieldset());
                        $setRow->addCss('fieldset', preg_replace('/[^a-z0-9_-]/i', '', $field->getFieldset()));
                    }
                    $this->showField($field, $setRow, 'fieldset');
                }
                $fieldsetName = $field->getFieldset();
                $i++;
            }
            if ($setRow) {
                $setRow->appendRepeat('tabBox');
            }
            $tabBox->appendRepeat('tab-content');
        }

        if (count($tabGroups)) {
            $t->setChoice('tabs');
            $tabPainName = $this->form->getId().'-tabPane';
            $t->setAttr('tabs', 'id', $tabPainName);
        }
    }


    /**
     * @param string $str
     * @return string
     */
    protected function cleanName($str)
    {
        return preg_replace('/[^a-z0-9]/i', '_', $str);
    }

    /**
     * @param array $fieldList
     * @return array
     */
    public function groupFieldset($fieldList)
    {
        $sets = array();

        /* @var $field Field\Iface */
        foreach ($fieldList as $name => $field) {
            $setName = $field->getFieldset();
            if (!isset($sets[$setName])) $sets[$setName] = array();
            $sets[$setName][$name] = $field;
        }
        $grouped = array();
        foreach ($sets as $fieldList) {
            foreach ($fieldList as $name => $field) {
                $grouped[$name] = $field;
            }
        }
        return $grouped;
    }

    /**
     * @param Field\Iface $field
     * @param Template $t
     * @param string $var
     * @throws \Exception
     */
    protected function showField(Field\Iface $field, Template $t, $var = 'fields')
    {
        if ($field instanceof Event\Iface) {
            $html = $field->show();
            /* @var Event\Iface $field */
            if ($html instanceof \Dom\Template) {
                $t->appendTemplate('events', $html);
            } else {
                $t->appendHtml('events', $html);
            }
        } else {
            // TODO: Making the Fields/renderers nestable could be a handy thing... ???
            $html = $html = $field->show();
            if ($this->getFieldGroupRenderer() && !$field instanceof Field\Hidden) {
                $this->getFieldGroupRenderer()->setField($field);
                $html = $this->getFieldGroupRenderer()->show();
            }
            if ($html instanceof \Dom\Template) {
                $t->appendTemplate($var, $html);
            } else {
                $t->appendHtml($var, $html);
            }
        }
    }

    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="">
	<script src="/vendor/ttek/tk-form/js/form.js" data-jsl-priority="-1"></script>


<form class="tk-form" var="form" role="form">
  <div class="alert alert-danger clear" choice="errors">
    <button data-dismiss="alert" class="close noblock">×</button>
    <h4><i class="icon-ok-sign"></i> <strong>Form Error</strong></h4>
    <span var="errors"></span>
  </div>
 
  <div class="tk-form-fields clearfix" var="fields">
  
    <div class="formTabs" var="tabs" choice="tabs">
      <div class="tab-content" var="tab-content">
        <div var="tabBox" repeat="tabBox" class="tab-pane">
          <fieldset var="fieldset" repeat="fieldset">
            <legend var="legend"></legend>
          </fieldset>
        </div>
      </div>
    </div>
    
    <fieldset var="fieldset" repeat="fieldset">
      <legend var="legend"></legend>
    </fieldset>
    
  </div>
  
  <div class="tk-form-events clearfix" var="events"></div>
</form>

</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }
}