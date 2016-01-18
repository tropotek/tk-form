<?php
namespace Tk\Form\Renderer;

use \Tk\Form;
use \Tk\Form\Field;
use \Tk\Form\Element;
use \Tk\Form\Exception;

/**
 * Class DomStatic
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class DomStatic extends Iface
{
    
    /**
     * @var \Dom\Form
     */
    protected $domForm = null;

    protected $formGroupErrorCss = 'has-error';
    protected $formErrorTextCss = 'text-danger';


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
     * @return DomStatic
     */
    static function create($form, $template)
    {
        return new static($form, $template);
    }


    /**
     * @param string $css
     * @return $this
     */
    public function setFormGroupErrorCss($css)
    {
        $this->formGroupErrorCss = $css;
        return $this;
    }

    /**
     * @param string $css
     * @return $this
     */
    public function setErrorTextCss($css)
    {
        $this->formErrorTextCss = $css;
        return $this;
    }


    /**
     * Render
     *
     * @return mixed
     */
    public function show()
    {
        if (!$this->domForm || !$this->domForm->getNode()) {
            return $this;
        }

        /* @var $field Field\Iface */
        foreach ($this->getForm()->getFieldList() as $field) {
            if (!$field instanceof Field\Iface) continue;
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
     * @param Element $field
     * @return mixed
     * @throws \Tk\Exception
     */
    protected function showField(Element $field)
    {
        if (!$field instanceof Field\Iface) {
            return;
        }
        
        /** @var Field\Iface $field */
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
            $this->domForm->getNode()->setAttribute('enctype', \Tk\Form::ENCTYPE_MULTIPART);
            return;
        }

        $value = $field->getValue();
        $elName = $field->getName();
        if (is_array($value) || $field->isArray()) {
            $elName = $field->getName() . '[]';
        }
        $elList = $this->domForm->getFormElementList($elName);
        
        /* @var $el \Dom\Form\Element */
        foreach ($elList as $i => $el) {
            if (!$el) continue;
            switch (get_class($el)) {
                case 'Dom\Form\Input' :
                    $nodeType = $el->getType();
                    if ($nodeType == 'checkbox' || $nodeType == 'radio') {
                        if (is_array($value) && $nodeType == 'checkbox') {
                            foreach ($value as $v) {
                                $this->domForm->setCheckedByValue($elName, $v);
                            }
                        } else {
                            $this->domForm->setCheckedByValue($elName, $value);
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
            $errNode->setAttribute('class', 'alert alert-danger ');
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
     * @param Field\Iface $field
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
                $node->parentNode->setAttribute('class', $node->parentNode->getAttribute('class') . ' ' . $this->formGroupErrorCss);
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
                $errNode->setAttribute('class', $this->formErrorTextCss);
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