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
class Renderer extends \Mod\Module
{

    const MSG_CLASS_ERROR     = 'error';
    const MSG_CLASS_WARNING   = 'warning';
    const MSG_CLASS_NOTICE    = 'notice';

    /**
     * @var \Form\Form
     */
    protected $form = null;


    /**
     * __construct
     *
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
        $this->setInstanceId($form->getInstanceId());
        $this->setInsertMethod(self::INS_REPLACE);
    }

    /**
     * Get the form object
     *
     * @return Form
     */
    public function getForm()
    {
    	return $this->form;
    }


    /**
     * Init the object
     */
    public function init()
    {
        tklog('Form_Renderer::init("'.$this->form->getId().'")');
        $this->form->init();
    }

    /**
     * Execute the object
     */
    public function doDefault()
    {
        tklog('Form_Renderer::doDefault("'.$this->form->getId().'")');
        $this->form->execute();
    }

    /**
     * Render
     *
     */
    public function show()
    {
        tklog('Form_Renderer::show("'.$this->form->getId().'")');
        
        $t = $this->getTemplate();

        $js = <<<JS
/**
 * Submit a form with an event attached so php scripts can fire the event.
 *
 * @param formElement form
 * @param string action
 * @param string value (optional) If not supplied, action is used.
 */
function tkFormSubmit(form, action) {
    var value = arguments[2] ? arguments[2] : action;
    if (!form) {
        return;
    }
    // Add the action event to a hidden field and value
    var node = document.createElement('input');
    node.setAttribute('type', 'hidden');
    node.setAttribute('name', action);
    node.setAttribute('value', value);
    form.appendChild(node);
    form.submit();
}
JS;
        $t->appendJs($js);

        $t->setAttr('form', 'id', $this->form->getId());

        if ($this->form->getMethod()) {
            $t->setAttr('form', 'method', $this->form->getMethod());
        }
        if ($this->form->getEnctype()) {
            $t->setAttr('form', 'enctype', $this->form->getEnctype());
        }
        if ($this->form->getEncoding()) {
            $t->setAttr('form', 'accept-charset', $this->form->getEncoding());
        }
        if ($this->form->getAction()) {
            $t->setAttr('form', 'action', $this->form->getAction());
        }
        if ($this->form->getTarget()) {
            $t->setAttr('form', 'target', $this->form->getTarget());
        }


        if ($this->form->getCssClassList()) {
            foreach ($this->form->getCssClassList() as $class) {
                $t->addClass('form', $class);
            }
        }
        if ($this->form->getTitle()) {
            $t->insertText('title', $this->form->getTitle());
            $t->setChoice('title');
        }



        if ($this->form->hasErrors()) {
            /* @var $field Field\Iface */
            foreach ($this->form->getFieldList() as $field) {
                if ($field->hasErrors()) {
                    $field->addFieldClass('error');
                }
            }

            $estr = '';
            foreach ($this->form->getErrors() as $error) {
                $estr = $error . "<br/>\n";
            }
            if ($estr) {
                $t->appendHtml('error', '<p style="margin: 0;padding: 0;">'.$estr.'</p>');
                $t->setChoice('error');
            }
        }

        $this->showEvents($t);
        $this->showFields($t);

    }

    /**
     * Render Buttons/Events
     *
     * @param \Dom\Template $t
     */
    public function showEvents(\Dom\Template $t)
    {
        $eventList = $this->form->getObservable()->getObserverList();
        /* @var $event Event\Iface */
        foreach ($eventList as $arr) {
            foreach ($arr as $event) {
                if (!$event instanceof Event\Iface || $event->isHidden()) {
                    continue;
                }
                $event->show();
                $t->appendTemplate('events', $event->getTemplate());
            }
        }
    }

    /**
     * Render Fields
     *
     * @param \Dom\Template $t
     */
    public function showFields(\Dom\Template $t)
    {
        $i = 0;

        $tabGroups = array();

        $fieldList = $this->groupFieldset($this->form->getFieldList());
        $fieldsetName = '____>__';
        $setRow = null;
        /* @var $field \Form\Field\Iface */
        foreach ($fieldList as $field) {
            if ($field->isHidden()) {
                continue;
            }
            //$field->show();
            if (!$field->getTabGroup()) {
                if (!$field->getFieldset()) {
                    $t->appendTemplate('fields', FieldRenderer::create($field)->getTemplate());
                } else {
                    if ($fieldsetName != $field->getFieldset()) {
                        if ($setRow) {
                            $setRow->appendRepeat();
                        }
                        $setRow = $t->getRepeat('fieldset');
                        $setRow->insertText('legend', $field->getFieldset());
                        $setRow->addClass('fieldset', $field->getFieldset());
                    }
                    $setRow->appendTemplate('fieldset', FieldRenderer::create($field)->getTemplate());
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
            $fieldsetName = '____>__';
            $setRow = null;
            foreach ($group as $field) {
                $tabBox->setAttr('tabBox', 'id', $this->form->getId().$this->cleanName($gname));
                $tabBox->setAttr('tabBox', 'data-name', $gname);
                if (!$field->getFieldset()) {
                    $tabBox->appendTemplate('tabBox', FieldRenderer::create($field)->getTemplate());
                } else {
                    if ($fieldsetName != $field->getFieldset()) {
                        if ($setRow) {
                            $setRow->appendRepeat();
                        }
                        $setRow = $tabBox->getRepeat('fieldset');
                        $setRow->insertText('legend', $field->getFieldset());
                        $setRow->addClass('fieldset', $field->getFieldset());
                    }
                    $setRow->appendTemplate('fieldset', FieldRenderer::create($field)->getTemplate());
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
     * cleanName
     *
     * @param string $str
     * @return string
     */
    protected function cleanName($str)
    {
        return preg_replace('/[^a-z0-9]/i', '_', $str);
        //return str_replace(array(' ', '-', '/', '\\', '.'), '_', $str);
    }

    /**
     * makeTemplate
     *
     * @return string
     */
    public function __makeTemplate()
    {
        // Bootstrap From and Tabs
        $xmlStr = <<<XML
<?xml version="1.0"?>
<div class="">
  <script type="text/javascript">
// \\From\\Renderer <![CDATA[
jQuery(function ($) {
    // Bootstrap Tabs
    $('.formTabs').each(function(id, tabContainer) {
        var ul = $('<ul class="nav nav-tabs"></ul>');

        $(tabContainer).find('.tab-pane').each(function (i, tbox) {
            var name = $(tbox).attr('data-name');
            var li = $('<li></li>');
            var a = $('<a></a>');
            a.attr('href', '#'+tbox.id);
            a.attr('data-toggle', 'tab');
            a.text(name);
            li.append(a);

            // Check for maps
            if ($(tbox).find('.tk-gmap-canvas').length) {
                li.addClass('isMap');
            }
            // Check for errors
            if ($(tbox).find('.error').length) {
                li.addClass('error');
            }
            if (i == 0) {
                $(tbox).addClass('active');
                li.addClass('active');
            }
            // TODO: Find first error tab and open that by default after page loading
            ul.append(li);
        });
        $(tabContainer).prepend(ul);
        $(tabContainer).tab('show');
    });
});
//]]>
</script>
  <form var="form" role="form" class="tk-form">

    <div class="alert alert-danger alert-dismissable" choice="error">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <div var="error"></div>
    </div>

    <div class="fieldBox" var="fields">
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
    <div class="form-actions events" var="events"></div>
  </form>
</div>
XML;

        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }


}
