<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 * A form text field object
 *
 * @package Form\Field
 * @deprecated
 */
class Mce extends Textarea
{

    /**
     * @var \TinyMce\TinyMce
     */
    protected $mce = null;


    /**
     * Construct
     *
     * @param string $name
     * @param \TinyMce $mce
     */
    public function __construct($name, $mce = null)
    {
        parent::__construct($name);
        $this->mce = $mce;
    }

    /**
     * 
     *
     * @param \TinyMce\Plugin $plugin
     * @return \Form\Field\Mce
     */
    public function enableFilemanager($plugin = null)
    {
        if (!$plugin) {
            $plugin = \TinyMce\Plugin\ElFinder::create();
        }
        $this->getMce()->addPlugin($plugin);
        return $this;
    }

    /**
     * Set the form for this element
     *
     * @param \Form\Form $form
     * @return Element
     */
    public function setForm(\Form\Form $form)
    {
        $r = parent::setForm($form);
        if (!$this->mce && class_exists('TinyMce\TinyMce')) {
            $this->mce = \TinyMce\TinyMce::createNormal('#' . $this->getId());
        }
        return $r;
    }


    /**
     * Get the tinymce renderer
     *
     * @return TinyMce
     */
    public function getMce()
    {
        return $this->mce;
    }

    /**
     * Render the widget.
     *
     */
    public function show()
    {

        if ($this->mce) {
            $this->mce->setTemplate($this->getTemplate());
            $this->mce->show();
        }

        parent::show();
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
<textarea name="" var="element" class="textareaMce"></textarea>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }

}