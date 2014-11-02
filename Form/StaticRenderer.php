<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form;

/**
 * The static form renderer.
 * It requires on the Dom_Form class
 *
 * This renderer requires that the form markup is already in place.
 *
 *
 * @package Form
 */
class StaticRenderer extends \Mod\Module
{

    const MSG_CLASS_ERROR     = 'error';
    const MSG_CLASS_WARNING   = 'warning';
    const MSG_CLASS_NOTICE    = 'notice';

    /**
     * @var \Form\Form
     */
    protected $form = null;



    /**
     * Create the object instance
     *
     * @param \Form\Form $form
     * @param \Dom\Template $template
     */
    public function __construct($form, $template)
    {
        $this->form = $form;
        $this->setTemplate($template);
        $this->setInstanceId($form->getInstanceId());
    }


    /**
     * Init the object
     */
    public function init()
    {
        $this->form->init();
    }

    /**
     * Execute the object
     */
    public function doDefault()
    {
        $this->form->execute();
    }

    /**
     * Render
     *
     */
    public function show()
    {
        $template = $this->getTemplate();

        $domForm = $template->getForm($this->form->getId());
        
        if (!$domForm || !$domForm->getNode()) {
            \Tk\Log\Log::write('Cannot find form: ' . $this->form->getId());
            return;
        }
        if ($this->form->getAction()) {
        	$domForm->setAction($this->form->getAction());
        }
        if ($this->form->getMethod()) {
        	$domForm->setMethod($this->form->getMethod());
        }
        if ($this->form->getEnctype()) {
        	$domForm->getNode()->setAttribute('enctype', $this->form->getEnctype());
        }
        if ($this->form->getEncoding()) {
        	$domForm->getNode()->setAttribute('accept-charset', $this->form->getEncoding());
        }

        $this->showFields($template, $domForm);

        $msg = '';
//        if ($this->form->hasErrors()) {
//            $msg = 'The form contains errors, please correct and try again. <br />';
//        }
        foreach ($this->form->getErrors() as $m) {
            $msg .= $m . ' <br />';
        }
        if ($msg) {
            $msg = substr($msg, 0, -6);
        }

        if ($msg || count($this->form->getErrors()) > 0) {
            $var = $choice = 'form-error';
            if ($template->keyExists('var', $var)) {
                $template->insertHtml($var, $msg);
                $template->setChoice($choice);
            } else {
                $errNode = $domForm->getNode()->ownerDocument->createElement('p');
                $errNode->setAttribute('class', 'alert alert-error');
                if ($domForm->getNode()) {
                    $child = $this->getFirstChildElement($domForm->getNode());
                    $domForm->getNode()->insertBefore($errNode, $child);
                    \Dom\Template::insertHtmlDom($errNode, $msg);
                }
            }
        }
    }


    /**
     * Render the form fields
     *
     * @param \Dom\Template $template
     * @param \Dom\Form $domForm
     * @throws \Tk\Exception
     * @return bool
     */
    private function showFields($template, $domForm)
    {
        $hasErrors = false;
        /* @var $field Field\Iface */
        foreach ($this->form->getFieldList() as $field) {
            if ($field instanceof Field\Hidden) {
                $domEl = $domForm->getFormElement($field->getName());
                if (!$domEl) {
                    $domForm->appendHiddenElement($field->getName(), $field->getValue());
                }
            }

            $values = $field->getType()->getFieldValues();
            foreach ($values as $name => $value) {
                if (is_array($value)) {
                    $elList = $domForm->getFormElementList($name . '[]');
                } else {
                    $elList = $domForm->getFormElementList($name);
                }
                /* @var $el \Dom\FormElement */
                foreach ($elList as $el) {
                    if ($el != null) {
                        $type = $el->getType();
                        switch (get_class($el)) {
                            case 'Dom\FormInput' :
                                if ($type == 'file') {
                                    break;
                                }
                                if ($type == 'checkbox' || $type == 'radio') {
                                    if (is_array($value) && $type == 'checkbox') {
                                        foreach ($value as $v) {
                                            $domForm->setCheckedByValue($name . '[]', $v);
                                        }
                                    } else {
                                        $domForm->setCheckedByValue($name, $value);
                                    }
                                } else {
                                    $el->setValue($value);
                                }
                                break;
                            case 'Dom\FormTextarea' :
                                $el->setValue($value);
                                break;
                            case 'Dom\FormSelect' :
                                $el->setValue($value);
                                break;
                        }
                    }
                }
            }

            // Render Errors
            if ($field->hasErrors()) {
                $msg = current($field->getErrors());
//                foreach ($field->getErrors() as $i => $m) {
//                    $msg .= $m;
//                    if ($i < count($field->getErrors()) - 1) {
//                        $msg .= '<br/>';
//                    }
//                }

                if ($msg != null) {
                    $el = $domForm->getFormElement($name);
                    if ($el == null) {
                        throw new \Tk\Exception('No form element: `' . $name . '` found. Check your validation field name parameters.');
                    }
                    $node = $el->getNode();
                    if ($node->parentNode && (strstr($node->parentNode->getAttribute('class'), 'required') ||
                        $node->parentNode->getAttribute('class') == 'optional'))
                    {
                        $node->parentNode->setAttribute('class', $node->parentNode->getAttribute('class') . ' error');
                    }
                    $var = $field->getName() . '-error';
                    if ($template->keyExists('var', $var)) {
                        $template->setChoice($var);
                        if ($template->keyExists('var', $var)) {
                            if (!$template->getText($var)) {
                                $template->insertHTML($var, $msg);
                            }
                        }
                    } else {
                        // <p class="error-text" choice="error"><span class="glyphicon glyphicon-ban-circle"></span> <span var="error"></span></p>
                        $errNode = $node->ownerDocument->createElement('p');
                        $errNode->setAttribute('class', 'error-text');
                        $icon = $node->ownerDocument->createElement('span');
                        $icon->setAttribute('class', 'glyphicon glyphicon-ban-circle');
                        $text = $node->ownerDocument->createElement('span');
                        $errNode->appendChild($icon);
                        $errNode->appendChild($text);
                        if ($node->parentNode) {
                            $errNode = $node->parentNode->insertBefore($errNode, $node);
                            \Dom\Template::insertHtmlDom($text, $msg);
                        }
                    }
                    $hasErrors = true;
                }
            }
        }
        return $hasErrors;
    }

    /**
     * getFirstChildElement
     *
     * @param DOMElement $parent
     * @return DOMNode
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