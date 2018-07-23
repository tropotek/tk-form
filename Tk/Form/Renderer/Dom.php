<?php
namespace Tk\Form\Renderer;

use Dom\Template;
use Tk\Form\Field;
use Tk\Form\Event;
use Tk\Form;

/**
 * Class Dom
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Dom extends Iface
{

    const DEFAULT_FIELD_TEMPLATE = '\Tk\Form\Renderer\FieldGroup';

    /**
     * @var string
     */
    protected $fieldGroupClass = self::DEFAULT_FIELD_TEMPLATE;


    /**
     * Create a new Renderer.
     *
     * @param Form $form
     * @return static
     * @deprecated I think this will be renamed or removed in the release version
     */
    static function create($form)
    {
        return new static($form);
    }

    /**
     * Render the field and return the template or html string
     *
     * @return \Dom\Template
     * @throws \Dom\Exception
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
     * @throws \Dom\Exception
     * @throws \ReflectionException
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
     * @param string $fieldGroupClass
     * @return $this
     */
    public function setFieldGroupClass($fieldGroupClass)
    {
        $this->fieldGroupClass = $fieldGroupClass;
        return $this;
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
     * @throws \Dom\Exception
     * @throws \ReflectionException
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
            // TODO: Add a no field group option to Field, then render as is with nor field group class
            // TODO: OR Better! add a fieldGroupClass param per instance of a field, allowing any field group per Field
            // TODO: Making the Fields/renderers nestable could be a handy thing too
            if ($field instanceof Field\Hidden) {
                $html = $field->show();
            } else {
                /** @var FieldGroup $fg */
                if (!$this->fieldGroupClass) $this->fieldGroupClass = self::DEFAULT_FIELD_TEMPLATE;
                $fg = new $this->fieldGroupClass($field);
                $html = $fg->show();
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
<script type="text/javascript"> //<![CDATA[
// TODO: move to an external script
// This is the Bootstrap Tab script
jQuery(function($) {
  
    $('.formTabs').each(function(id, tabContainer) {
        var ul = $('<ul class="nav nav-tabs"></ul>');
        var errorSet = false;
        
        $(tabContainer).find('.tab-pane').each(function (i, tbox) {
            var name = $(tbox).attr('data-name');
            var li = $('<li></li>');
            var a = $('<a></a>');
            a.attr('href', '#'+tbox.id);
            a.attr('data-toggle', 'tab');
            a.text(name);
            li.append(a);
            
            // Check for errors
            if ($(tbox).find('.has-error').length) {
                li.addClass('has-error');
            }
            if (i === 0) {
                $(tbox).addClass('active');
                li.addClass('active');
            }
            ul.append(li);
        });
        $(tabContainer).prepend(ul);
        $(tabContainer).find('li.has-error a');
        
        //$(tabContainer).find('li.has-error a').tab('show'); // shows last error tab
        $(tabContainer).find('li.has-error a').first().tab('show');   // shows first error tab
    });
    
    // Deselect tab
    $('.formTabs li a').on('click', function (e) { $(this).trigger('blur'); });
});
//]]></script>


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