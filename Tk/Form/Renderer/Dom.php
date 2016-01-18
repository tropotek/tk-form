<?php
namespace Tk\Form\Renderer;

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
class Dom extends \Tk\Form\Renderer\Iface
{

    /**
     * Create a new Renderer.
     *
     * @param Form $form
     * @return Dom
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
            return;
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
            $t->addClass('form', $v);
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
                if ($error)
                    $estr = $error . "<br/>\n";
            }
            if ($estr) {
                $estr = substr($estr, 0, -6);
                $t->appendHtml('errors', '<p>'.$estr.'</p>');
                $t->setChoice('errors');
            }
        }

        /* @var $field Field\Iface */
        foreach ($this->getForm()->getFieldList() as $field) {
            $this->showField($field);
        }

        return $this;
    }

    /**
     * Render Fields
     *
     * @param Form\Element $field
     * @return mixed
     */
    protected function showField(Form\Element $field)
    {
        $t = $this->getTemplate();
        $html = $field->getHtml();
        
        if ($field instanceof Event\Iface) {
            /** @var Event\Iface $field */
            if ($html instanceof \Dom\Template) {
                $t->appendTemplate('events', $html);
            } else {
                $t->appendHtml('events', $html);
            }
        } else {
            /** @var Field\Iface $field */
            // TODO: Check this is how we want to do this, I would like to see the ability to override the FieldGroup object
            $fg = new FieldGroup($field);
            $html = $fg->show();
            
            
            if ($html instanceof \Dom\Template) {
                $t->appendTemplate('fields', $html);
            } else {
                $t->appendHtml('fields', $html);
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
        $xhtml = <<<XHTML
<form class="tk-form" var="form" role="form">
  <div class="tk-form-errors" choice="errors" var="errors"></div>

  <div class="tk-form-fields" var="fields"></div>

  <div class="tk-form-events" var="events"></div>
</form>
XHTML;

        return \Dom\Loader::load($xhtml);
    }
}