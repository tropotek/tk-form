<?php
namespace Tk\Form\Renderer\Dom;

use \Tk\Form;
use \Tk\Form\Type;
use \Tk\Form\Field;
use \Tk\Form\Exception;

/**
 * The static form renderer.
 * It requires on the Dom_Form class
 *
 * This renderer requires that the form markup is already in place.
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class FormStatic extends \Tk\Form\Renderer\Iface
{

    const MSG_CLASS_ERROR     = 'error';
    const MSG_CLASS_WARNING   = 'warning';
    const MSG_CLASS_NOTICE    = 'notice';

    /**
     * @var \Dom\Form
     */
    protected $domForm = null;



    /**
     * Create the object instance
     *
     * @param Form $form
     * @param \Dom\Template $template
     */
    public function __construct($form, $template)
    {
        parent::__construct($form);
        $this->setTemplate($template);
        $this->domForm = $template->getForm($this->form->getId());
    }

    /**
     * Create a new Renderer.
     *
     * @param Form $form
     * @param \Dom\Template $template The template where the form resides
     * @return Form
     */
    static function create($form, $template)
    {
        return new static($form, $template);
    }

    /**
     * Render
     *
     * @return mixed
     */
    public function show()
    {
        if (!$this->domForm || !$this->domForm->getNode()) {
            return;
        }

        /* @var $field Field\Iface */
        foreach ($this->getForm()->getFieldList() as $field) {
            $this->showField($field);
        }

        // Render Form Errors
        if (count($this->getForm()->getErrors()) > 0) {
            $this->showFormError();
        }

        return $this;
    }

    /**
     * Render the form field values
     *
     * @param Field\Iface $field
     * @return mixed
     * @throws \Tk\Exception
     */
    protected function showField(Field\Iface $field)
    {
        $elName = $field->getName();
        if ($field->isArray()) {
            $elName .= '[]';
        }

        // If field does not exist add a hidden field with its value?
        if (!$this->domForm->getFormElement($elName)) {
            $this->domForm->appendHiddenElement($field->getName(), $field->getValue());
            $this->domForm->getFormElement($field->getName());
        }

        if ($field instanceof \Tk\Form\Field\File) {
            // Check form enctype exists
            $this->domForm->getNode()->setAttribute('enctype', \Tk\Form\Form::ENCTYPE_MULTIPART);
            return;
        }

        $values = $field->getType()->getTextValue();
        foreach ($values as $name => $value) {
            if (is_array($value)) {
                $elList = $this->domForm->getFormElementList($name . '[]');
            } else {
                $elList = $this->domForm->getFormElementList($name);
            }
            /* @var $el \Dom\Form\Element */
            foreach ($elList as $i => $el) {
                if (!$el) continue;
                $nodeType = $el->getType();
                switch (get_class($el)) {
                    case 'Dom\Form\Input' :
                        if ($nodeType == 'file') {
                            // Check form enctype exists
                            $this->domForm->getNode()->setAttribute('enctype', \Tk\Form\Form::ENCTYPE_MULTIPART);
                            break;
                        }
                        if ($nodeType == 'checkbox' || $nodeType == 'radio') {
                            if (is_array($value) && $nodeType == 'checkbox') {
                                foreach ($value as $v) {
                                    $this->domForm->setCheckedByValue($name . '[]', $v);
                                }
                            } else {
                                $this->domForm->setCheckedByValue($name, $value);
                            }
                        } else {
                            if (is_array($value)) {
                                if (count($value)) {
                                    $el->setValue($value[$i]);
                                }
                            } else {
                                $el->setValue($value);
                            }
                        }
                        break;
                    case 'Dom\Form\Textarea' :
                        $el->setValue($value);
                        break;
                    case 'Dom\Form\Select' :
                        $el->setValue($value);
                        break;
                }
            }
        }

        // Render Errors
        if ($field->hasErrors()) {
            $this->showError($field);
        }
    }

    /**
     * Show the overall form error if set
     */
    protected function showFormError()
    {
        $msg = '';
        foreach ($this->getForm()->getErrors() as $m) {
            $msg .= $m . ' <br />';
        }
        if ($msg) {
            $msg = substr($msg, 0, -6);
        }
        if (!$msg) return;
        $var = $choice = 'form-error';
        if ($this->getTemplate()->keyExists('var', $var)) {
            $this->getTemplate()->insertHtml($var, $msg);
            $this->getTemplate()->setChoice($choice);
        } else {
            $errNode = $this->domForm->getNode()->ownerDocument->createElement('div');
            $errNode->setAttribute('class', 'alert alert-error');
            if ($this->domForm->getNode()) {
                $child = $this->getFirstChildElement($this->domForm->getNode());
                $this->domForm->getNode()->insertBefore($errNode, $child);
                \Dom\Template::insertHtmlDom($errNode, $msg);
            }
        }
    }

    /**
     *
     *
     * @param Field $field
     * @throws Exception
     */
    protected function showError($field)
    {
        $msg = '';
        foreach ($field->getErrors() as $m) {
            $msg .= htmlentities($m) . ' <br />';
        }
        if ($msg) {
            $msg = substr($msg, 0, -6);
        }

        if ($msg != null) {
            $el = $this->domForm->getFormElement($field->getName());
            if ($field->isArray()) {
                $el = $this->domForm->getFormElement($field->getName().'[]');
            }
            if ($el == null) {
                throw new Exception('Form element: `' . $field->getName() . '` not found. Check your validation field name parameters.');
            }
            $node = $el->getNode();
            // TODO: iterate up the tree to find the 'form-group' node
            if ($node->parentNode && strstr($node->parentNode->getAttribute('class'), 'form-group')) {
                $node->parentNode->setAttribute('class', $node->parentNode->getAttribute('class') . ' has-error');
            }
            $var = $field->getName() . '-error';
            if ($this->template->keyExists('var', $var)) {
                $this->template->setChoice($var);
                if ($this->template->keyExists('var', $var)) {
                    if (!$this->template->getText($var)) {
                        $this->template->insertHTML($var, $msg);
                    }
                }
            } else {
                $errNode = $node->ownerDocument->createElement('div');
                $errNode->setAttribute('class', 'text-danger');
                $text = $node->ownerDocument->createElement('span');
                $errNode->appendChild($text);
                if ($node->parentNode) {
                    $node->parentNode->insertBefore($errNode, $node);
                    \Dom\Template::insertHtmlDom($text, $msg);
                }
            }
        }
    }

    /**
     * getFirstChildElement
     *
     * @param \DOMElement $parent
     * @return \DOMNode
     */
    public function getFirstChildElement($parent)
    {
        foreach ($parent->childNodes as $node) {
            if ($node->nodeType == \XML_ELEMENT_NODE) {
                return $node;
            }
        }
    }

}