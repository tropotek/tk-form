<?php
namespace Tk\Form\Renderer\Dom;

use Tk\Form\Field;
use Tk\Form\Type;

/**
 * A Dom Renderer for the form object
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Form extends \Tk\Form\Renderer\Iface
{

    /**
     * Create a new Renderer.
     *
     * @param \Tk\Form $form
     * @return Form
     */
    static function create($form)
    {
        return new static($form);
    }

    /**
     * Render the field and return the template or html string
     *
     * @return mixed
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
     * @param Field\Iface $field
     * @return mixed
     */
    protected function showField(Field\Iface $field)
    {
        $t = $this->getTemplate();

        if (!$field->getRenderer() instanceof \Dom\Renderer\Iface) {
            return;
        }

        $field->getRenderer()->show();

        if ($field instanceof Field\Event) {
            $t->appendTemplate('events', $field->getRenderer()->getTemplate());
        } else {
            $t->appendTemplate('fields', $field->getRenderer()->getTemplate());
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