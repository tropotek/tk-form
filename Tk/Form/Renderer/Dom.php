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
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Dom extends Iface
{
    /**
     * @var string
     */
    protected $fieldGroupClass = \Tk\Form\Renderer\FieldGroup::class;


    /**
     * Create a new Renderer.
     *
     * @param Form $form
     * @return Dom
     * @deprecated I think this will be renamed or removed in the release version
     */
    static function create($form)
    {
        return new static($form);
    }

    /**
     * Render the field and return the template or html string
     *
     * @return $this
     */
    public function show()
    {
        $t = $this->getTemplate();
        if (!$t->keyExists('var', 'form')) {
            return $this;
        }

        // Field name attribute
        $t->setAttr('form', 'id', $this->getForm()->getId());

        // All other attributes
        foreach($this->getForm()->getAttrList() as $key => $val) {
            if ($val == '' || $val == null) {
                $val = $key;
            }
            $t->setAttr('form', $key, $val);
        }

        // Element css class names
        foreach($this->getForm()->getCssList() as $v) {
            $t->addCss('form', $v);
        }

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
                $t->appendHtml('errors', $estr);
                $t->setChoice('errors');
            }
        }

        $this->showFields($t);

        return $this;
    }



    /**
     * Render Fields
     *
     * @param Template $t
     */
    public function showFields(Template $t)
    {
        $i = 0;
        $tabGroups = array();

        $fieldList = $this->groupFieldset($this->form->getFieldList());
        $fieldsetName = 'null';
        $setRow = null;
        /* @var $field Field\Iface */
        foreach ($fieldList as $field) {
            if (!$field->getTabGroup()) {
                if (!$field->getFieldset()) {
                    $this->showField($field, $t);
                } else {
                    if ($fieldsetName != $field->getFieldset()) {
                        if ($setRow) {
                            $setRow->appendRepeat();
                        }
                        $setRow = $t->getRepeat('fieldset');
                        $setRow->insertText('legend', $field->getFieldset());
                        $setRow->addCss('fieldset', $field->getFieldset());
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
            $setRow->appendRepeat();
        }

        $i = (count($tabGroups)%2) ? 0 : 1;
        foreach ($tabGroups as $gname => $group) {

            $tabBox = $t->getRepeat('tabBox');
            $fieldsetName = 'null';
            $setRow = null;
            foreach ($group as $field) {
                $tabBox->setAttr('tabBox', 'id', $this->form->getId().$this->cleanName($gname));
                $tabBox->setAttr('tabBox', 'data-name', $gname);
                if (!$field->getFieldset()) {
                    $this->showField($field, $tabBox, 'tabBox');
                } else {
                    if ($fieldsetName != $field->getFieldset()) {
                        if ($setRow) {
                            $setRow->appendRepeat();
                        }
                        $setRow = $tabBox->getRepeat('fieldset');
                        $setRow->insertText('legend', $field->getFieldset());
                        $setRow->addCss('fieldset', $field->getFieldset());
                    }
                    $this->showField($field, $setRow, 'fieldset');
                }
                $i++;
            }
            if ($setRow) {
                $setRow->appendRepeat();
            }
            $tabBox->appendRepeat();
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
     * cleanName
     *
     * @param string $str
     * @return string
     */
    protected function cleanName($str)
    {
        return preg_replace('/[^a-z0-9]/i', '_', $str);
    }

    /**
     *
     *
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
     * Render Fields
     *
     * @param Field\Iface $field
     * @param Template $t
     * @return mixed
     */
    protected function showField(Field\Iface $field, Template $t, $var = 'fields')
    {
        if ($field instanceof Event\Iface) {
            $html = $field->getHtml();
            /* @var Event\Iface $field */
            if ($html instanceof \Dom\Template) {
                $t->appendTemplate('events', $html);
            } else {
                $t->appendHtml('events', $html);
            }
        } else {

            $fg = new $this->fieldGroupClass($field);
            $html = $fg->show();
            
            if ($html instanceof \Dom\Template) {
                $t->appendTemplate($var, $html);
            } else {
                $t->appendHtml($var, $html);
            }
        }
    }

    /**
     * makeTemplate
     *
     * @return string
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="">
<script type="text/javascript"> //<![CDATA[
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
            if (i == 0) {
                $(tbox).addClass('active');
                li.addClass('active');
            }
            ul.append(li);
        });
        $(tabContainer).prepend(ul);
        $(tabContainer).find('li.has-error a').css('color', '#a94442').css('font-weight', 'bold');
        
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
 
  <div class="tk-form-fields" var="fields">
    <div class="formTabs" var="tabs" choice="tabs">
      <div class="tab-content">
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
    
  <!-- div class="tk-form-fields" var="fields"></div -->

  <div class="tk-form-events" var="events"></div>
</form>

</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }
}