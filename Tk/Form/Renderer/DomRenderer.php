<?php
namespace Tk\Form\Renderer;

use Dom\Template;
use Tk\Collection;
use Tk\Form\Field;
use Tk\Form\Event;
use Tk\Form;

/**
 * --- EXPERIMENTAL ----
 * This is the new Form renderer that renderes the fields in order
 * Non tabed and fieldset fields should be rendered after fieldsets and tabgroups
 * if they are added after them, the Dom.php renderers non-grouped fields together for some reason
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 * @deprecated Testing class to see if I can fix the fields render order (For the ROT Entry Form)
 */
class DomRenderer extends Iface
{

    /**
     * @var null|\Dom\Repeat
     */
    protected $formRow = null;

    protected $fieldsetTemplates = [];

    protected $tabGroupTemplates = [];

    protected $renderQueue = [];


    /**
     * @param Form $form        //TODO: Once we remove the setFieldGroupRenderer() we can remove the form and use setForm()
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
     * Render the field and return the template or html string
     *
     * @return \Dom\Template
     * @throws \Exception
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
                $template->setVisible('errors');
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
        // get fields
        $tree = $this->makeFieldRenderTree($this->form->getFieldList());
        //vd(\Tk\Debug\VarDump::varToString($tree));
        /* @var $fields Field\Iface|array */
        foreach ($tree as $name => $fields) {
            //\Tk\Log::debug($field->getName() . sprintf('[%s] [%s] ', $field->getTabGroup(), $field->getFieldset()));
            if (is_array($fields) && substr($name,0 , 3) == 'tg-') {     // Render Tab group
                $tpl = $this->renderTabGroup(substr($name, 3), $fields);
            } else if (is_array($fields) && substr($name,0 , 3) == 'fs-') {  // Render Fieldset
                $tpl = $this->renderFieldset(substr($name, 3), '', $fields);
            } else {    // render single field
                $tpl = $this->renderField($fields);
            }
//vd($tpl->toString(false));
            if ($fields instanceof Event\Iface || $fields instanceof Field\Hidden) {
                $t->appendTemplate('events', $tpl);
            } else {
                $t->appendTemplate('fields', $tpl);
            }

        }
    }



    /**
     * @param array $fieldList
     * @return array
     */
    public function makeFieldRenderTree($fieldList)
    {
        $sets = array();
        $rowId = 0;
        /* @var $field Field\Iface */
        foreach ($fieldList as $name => $field) {

            $layoutCol = $this->getLayout()->getCol($field->getName());
            if (!$layoutCol || $layoutCol->isRowEnabled()) {
                $rowId++;
            }
            //\Tk\Log::debug($field->getName() . sprintf('[%s] [%s] ', $field->getTabGroup(), $field->getFieldset()));
            if ($field->getTabGroup()) {
                if ($field->getFieldset()) {
                    // has tabGroup and fieldset
                    $sets['tg-' . $field->getTabGroup()]['fs-' . $field->getFieldset()]['fields']['row-'.$rowId][] = $field;
                } else {
                    // has tabgroup only
                    $sets['tg-' . $field->getTabGroup()]['row-'.$rowId][] = $field;
                }
            } else {
                if ($field->getFieldset()) {
                    // has fieldset
                    $sets['fs-' . $field->getFieldset()]['row-'.$rowId][] = $field;
                } else {
                    // standalone field row
                    $sets['row-'.$rowId][] = $field;
                }
            }

        }
        return $sets;
    }

    /**
     *  array [
     *     'fs-...' => [  .. Array Of fieldsets ... ]
     *     'fields' =>  [ ... Array Of fields ... ]
     *  ]
     *
     * @param string $tabGroup
     * @param Field\Iface[]|array $fields
     * @param Template $t
     */
    protected function renderTabGroup($tabGroup, $fields)
    {
        $queue = [];
        $t = $this->getTabGroupTemplate($tabGroup);
        //vd(\Tk\Debug\VarDump::varToString($tree));

        // TODO: Render tabGroup details

        foreach ($fields as $name => $tree) {
            if (is_array($fields) && substr($name,0 , 3) == 'fs-') {  // Render Fieldset
                $tpl = $this->renderFieldset(substr($name, 3), $tabGroup, $fields);
            } else {    // render single field
                $tpl = $queue[$name] = $this->renderField($fields);
            }
            $t->appendTemplate('tabGroup', $tpl);
        }
        return $t;
    }

    /**
     *  array [
     *     'fs-...' => [  .. Array Of fieldsets ... ]
     *     'fields' =>  [ ... Array Of fields ... ]
     *  ]
     *
     * @param string $fieldset
     * @param string $tabGroup
     * @param Field\Iface[]|array $fields
     * @param Template $t
     */
    protected function renderFieldset($fieldset, $tabGroup = '', $fields)
    {
//        vd($fieldset);
//        vd(\Tk\Debug\VarDump::varToString($fields, 2));
        $t = $this->getFieldSetTemplate($fieldset, $tabGroup);

        // TODO: Render fieldset details
        $t->insertText('legend', $fieldset);
        $t->addCss('fieldset', preg_replace('/[^a-z0-9_-]/i', '', $fieldset));

        $css = '';
        /** @var Field\Iface[] $children */
        foreach ($fields as $children) {
            $css = $children[0]->getFieldsetCss();
            $t->appendTemplate('fieldset', $this->renderField($children));
        }
        $t->addCss('fieldset', $css);
        vd($t->toString(false));
        return $t;
    }

    /**
     * @param Field\Iface[]|array $fields
     * @param Template $t
     */
    protected function renderField($fields)
    {
        //vd(\Tk\Debug\VarDump::varToString($field, 2));
        $t = $this->getFieldTemplate();

        foreach ($fields as $field) {
            $html = $field->show();
            if ($field instanceof Event\Iface || $field instanceof Field\Hidden) {
                return $html;
            }
            if ($this->getFieldGroupRenderer()) {
                $this->getFieldGroupRenderer()->setLayoutCol(null);
                if ($this->getLayout()) {
                    $this->getFieldGroupRenderer()->setLayoutCol($this->getLayout()->getCol($field->getName()));
                }
                $this->getFieldGroupRenderer()->setField($field);
                $html = $this->getFieldGroupRenderer()->show();
            }
            if ($html instanceof \Dom\Template) {
                $t->appendTemplate('form-row', $html);
            } else {
                $t->appendHtml('form-row', $html);
            }
        }


        // TODO: Render field-row details
//        $t->addCss('form-row', 'tk-' . lcfirst(\Tk\ObjectUtil::basename($field)) . '-row');
//        $t->addCss('form-row', 'tk-' . $field->getId() . '-row');

        return $t;
    }


    /**
     * @param string $fieldname
     * @param string $fieldset
     * @param string $tabgroup
     * @return string
     */
    private function hash($fieldname, $fieldset = '', $tabgroup = '')
    {
        return md5(sprintf('%s%s%s', $fieldname, $fieldset, $tabgroup));
    }





    /**
     * @param array $fieldList
     * @return array
     */
    public function groupFieldset1($fieldList)
    {
        $sets = array();

        /* @var $field Field\Iface */
        foreach ($fieldList as $name => $field) {
            $setName = $field->getFieldset();
            if (!$setName) {
                $sets[][$name] = $field;
                continue;
            }
            if ($setName && !isset($sets[$setName])) $sets[$setName] = array();
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
     * @param Template $t
     * @throws \Exception
     */
    public function showFields1(Template $t)
    {
        $i = 0;
        $tabGroups = array();

        $fieldList = $this->groupFieldset($this->form->getFieldList());
        $fieldsetName = 'null';
        $this->formRow = null;
        /** @var \Dom\Repeat $setRow */
        $setRow = null;
        /* @var $field Field\Iface */
        foreach ($fieldList as $field) {
          //  \Tk\Log::debug($field->getName() . ' [' . $field->getFieldset() . ']');

            if (!$field->getTabGroup()) {

                if ($fieldsetName != $field->getFieldset()) {
                    if ($this->formRow) {
                        $this->formRow->appendRepeat();
                        $this->formRow = null;
                    }
                    if ($setRow) {
                        $setRow->appendRepeat('fields');
                        $setRow = null;
                    }
                    if ($setRow == null && $field->getFieldset()) {
                        $setRow = $t->getRepeat('fieldset');
                        $setRow->insertText('legend', $field->getFieldset());
                        $setRow->addCss('fieldset', $field->getFieldsetCss());
                        $setRow->addCss('fieldset', preg_replace('/[^a-z0-9_-]/i', '', $field->getFieldset()));
                    }
                }

                if (!$field->getFieldset()) {
                    $this->showField($field, $t, 'fields');
                } else {
                    $this->showField($field, $setRow, 'fieldset');
                }


//                if (!$field->getFieldset()) {
//                    $this->showField($field, $t, 'fields');
//                } else {
//                    if ($fieldsetName != $field->getFieldset()) {
//                        if ($this->formRow) {
//                            $this->formRow->appendRepeat();
//                            $this->formRow = null;
//                        }
//                        if ($setRow) {
//                            $setRow->appendRepeat('fields');
//                        }
//                        $setRow = $t->getRepeat('fieldset');
//                        $setRow->insertText('legend', $field->getFieldset());
//                        $setRow->addCss('fieldset', $field->getFieldsetCss());
//                        $setRow->addCss('fieldset', preg_replace('/[^a-z0-9_-]/i', '', $field->getFieldset()) );
//
//                    }
//                    $this->showField($field, $setRow, 'fieldset');
//                }

                $fieldsetName = $field->getFieldset();
                $i++;


            } else {
                if (!isset($tabGroups[$field->getTabGroup()])) {
                    $tabGroups[$field->getTabGroup()] = array();
                }
                $tabGroups[$field->getTabGroup()][] = $field;
            }
        }
        if ($this->formRow) {
            $this->formRow->appendRepeat();
            $this->formRow = null;
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
            if (!$tabBox)
                throw new \Tk\Exception('No tabBox repeat available: `' . $gname . '`. Check you have not double parsed the template.');
            foreach ($group as $field) {
                $tabBox->setAttr('tabBox', 'id', $this->form->getId().$this->cleanName($gname));
                $tabBox->setAttr('tabBox', 'data-name', $gname);
                if (!$field->getFieldset()) {
                    $this->showField($field, $tabBox, 'tabBox');
                } else {
                    if ($fieldsetName != $field->getFieldset()) {
                        if ($this->formRow) {
                            $this->formRow->appendRepeat();
                            $this->formRow = null;
                        }
                        if ($setRow) {
                            $setRow->appendRepeat('tabBox');
                        }
                        $setRow = $tabBox->getRepeat('fieldset');
                        $setRow->insertText('legend', $field->getFieldset());
                        $setRow->addCss('fieldset', $field->getFieldsetCss());
                        $setRow->addCss('fieldset', preg_replace('/[^a-z0-9_-]/i', '', $field->getFieldset()));
                    }
                    $this->showField($field, $setRow, 'fieldset');
                }
                $fieldsetName = $field->getFieldset();
                $i++;
            }
            if ($this->formRow) {
                $this->formRow->appendRepeat();
                $this->formRow = null;
            }
            if ($setRow) {
                $setRow->appendRepeat('tabBox');
            }
            $tabBox->appendRepeat('tab-content');
        }

        if (count($tabGroups)) {
            $t->setVisible('tabs');
            $tabPainName = $this->form->getId().'-tabPane';
            $t->setAttr('tabs', 'id', $tabPainName);
        }
    }

    /**
     * @param Field\Iface $field
     * @param Template $t
     * @param string $var ???? Not being used???
     */
    protected function showField1(Field\Iface $field, Template $t, $var = 'fields')
    {
        if ($field instanceof Event\Iface || $field instanceof Field\Hidden) {
            $html = $field->show();
            if ($html instanceof \Dom\Template) {
                $t->appendTemplate('events', $html);
            } else {
                $t->appendHtml('events', $html);
            }
        } else {
            // Or use a layout adapter type object
            $html = $field->show();
            if ($this->getFieldGroupRenderer()) {
                $this->getFieldGroupRenderer()->setLayoutCol(null);
                if ($this->getLayout()) {
                    $this->getFieldGroupRenderer()->setLayoutCol($this->getLayout()->getCol($field->getName()));
                }
                $this->getFieldGroupRenderer()->setField($field);
                $html = $this->getFieldGroupRenderer()->show();
            }

            if (!$this->getLayout()) {
                $formRow = $t->getRepeat('form-row');
                $formRow->addCss('form-row', 'tk-'.\Tk\ObjectUtil::basename($field) . '-row');
                $formRow->addCss('form-row', 'tk-'.$field->getId() . '-row');
                if ($html instanceof \Dom\Template) {
                    $formRow->appendTemplate('form-row', $html);
                } else {
                    $formRow->appendHtml('form-row', $html);
                }
                $formRow->appendRepeat();
            } else {
                $layoutCol = $this->getLayout()->getCol($field->getName());
                if ($this->formRow && $layoutCol->isRowEnabled()) {
                    $this->formRow->appendRepeat();
                    $this->formRow = null;
                }
                if (!$this->formRow || $layoutCol->isRowEnabled()) {
                    $this->formRow = $t->getRepeat('form-row');
                    $this->formRow ->addCss('form-row', 'tk-'.lcfirst(\Tk\ObjectUtil::basename($field)) . '-row');
                    $this->formRow ->addCss('form-row', 'tk-'.$field->getId() . '-row');
                }

                if ($html instanceof \Dom\Template) {
                    $this->formRow->appendTemplate('form-row', $html);
                } else {
                    $this->formRow->appendHtml('form-row', $html);
                }
            }

        }
    }

    /**
     * [used in showFields1()]
     * @param string $str
     * @return string
     */
    protected function cleanName($str)
    {
        return preg_replace('/[^a-z0-9]/i', '_', $str);
    }



    /**
     * @return \Dom\Template
     */
    public function getFieldTemplate()
    {
        $xhtml = <<<HTML
<div class="form-row" var="form-row"></div>
HTML;
        return \Dom\Loader::load($xhtml);
    }

    /**
     * @return \Dom\Template
     */
    public function getFieldsetTemplate($fieldset, $tabGroup = '')
    {
        if (!isset($this->fieldsetTemplates[$tabGroup][$fieldset])) {
            $xhtml = <<<HTML
    <fieldset var="fieldset">
      <legend var="legend"></legend>
    </fieldset>
HTML;
            $this->fieldsetTemplates[$tabGroup][$fieldset] =  \Dom\Loader::load($xhtml);
        }
        return $this->fieldsetTemplates[$tabGroup][$fieldset];
    }

    /**
     * @return \Dom\Template
     */
    public function getTabGroupTemplate($tabGroup)
    {
        if (!isset($this->tabGroupTemplates[$tabGroup])) {
            $xhtml = <<<HTML
<div class="formTabs" var="tabs" choice="tabs">
  <div class="tab-content" var="tab-content">
    <div class="tab-pane" var="tabGroup" var-old="tabBox"></div>
  </div>
</div>
HTML;
            $this->tabGroupTemplates[$tabGroup] = \Dom\Loader::load($xhtml);
        }
        return $this->tabGroupTemplates[$tabGroup];
    }

    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div>
  <form class="tk-form" var="form" role="form">
    <div class="alert alert-danger clear" choice="errors">
      <button data-dismiss="alert" class="close noblock">×</button>
      <h4><i class="icon-ok-sign"></i> <strong>Form Error</strong></h4>
      <span var="errors"></span>
    </div>

    <div class="tk-form-fields clearfix" var="fields"></div>

<!--      <div class="formTabs" var="tabs" choice="tabs">-->
<!--        <div class="tab-content" var="tab-content">-->
<!--          <div var="tabBox" repeat="tabBox" class="tab-pane">-->
<!--          -->
<!--            <fieldset var="fieldset" repeat="fieldset">-->
<!--              <legend var="legend"></legend>-->
<!--              <div class="form-row" var="form-row" repeat="form-row"></div>-->
<!--            </fieldset>-->
<!--            -->
<!--            <div class="form-row" var="form-row" repeat="form-row"></div>-->
<!--            -->
<!--          </div>-->
<!--        </div>-->
<!--      </div>-->

<!--      <fieldset var="fieldset" repeat="fieldset">-->
<!--        <legend var="legend"></legend>-->
<!--          <div class="form-row" var="form-row" repeat="form-row"></div>-->
<!--      </fieldset>-->
<!--      -->
<!--      <div class="form-row" var="form-row" repeat="form-row"></div>-->      
<!--    </div>-->

    <div class="form-row tk-form-events clearfix" var="events"></div>
  </form>

</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }
}
