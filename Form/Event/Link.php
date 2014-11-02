<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Event;

/**
 * All events created from this base buttone event use the name
 * as the hook for when to execute after a form submission.
 *
 * For example if the button name is `save` then once pressed and the page is submitted
 * the form event controller will look through the $_REQUEST parameters and if the key `save`
 * is present then this event will be executed.
 *
 * @package Form\Event
 */
class Link extends Button
{


    /**
     * __construct
     *
     * @param string $name
     * @param string $icon
     */
    public function __construct($name, $icon = '')
    {
        parent::__construct($name, $icon);
    }


    /**
     * execute
     *
     * @param \Form\Form $form
     */
    public function update($form)
    {
        if (!$form->getRedirectUrl()) {
            $form->setRedirectUrl($this->getRedirectUrl());
        }
    }


    /**
     * Render the default attributes of an element
     *
     */
    public function show()
    {
        $t = $this->getTemplate();

        if (!$t->keyExists('var', 'element')) {
            return;
        }

        $t->insertText('text', $this->getLabel());
        //$t->setAttr('element', 'href', $this->getRedirectUrl());
        $t->setAttr('element', 'title', $this->getLabel());
        $t->setAttr('element', 'id', $this->getId());

        $js = "tkFormSubmit(document.getElementById('{$this->form->getId()}'), '{$this->getName()}');return false;";
        $t->setAttr('element', 'onclick', $js);

        if (!$this->enabled) {
            $t->setAttr('element', 'title', 'disabled');
            $t->setAttr('element', 'onclick', 'return false;');
        }
        if ($this->tabindex > 0) {
            $t->setAttr('element', 'tabindex', $this->tabindex);
        }
        foreach ($this->attrList as $attr => $js) {
            $t->setAttr('element', $attr, $js);
        }
        foreach ($this->cssList as $v) {
            $t->addClass('element', $v);
        }

        if ($this->icon) {
            $t->setChoice('icon');
            $t->addClass('icon', $this->icon);
        }


//        $js = <<<JS
//jQuery(function($) {
//  // Disable form edits messages
//  $('#{$this->getId()}').click(function(){
//      $(window).unbind('beforeunload');
//  });
//});
//JS;
//        $t->appendJs($js);

    }

    /**
     * makeTemplate
     *
     * @return string
     */
    public function __makeTemplate()
    {
        $xmlStr = <<<XML
<?xml version="1.0"?>
<a href="javascript:;" var="element" class="tk-btnLink"><span var="text">Submit</span></a>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }

}