<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 * A form Html renderer field object
 * This method can be used to insert custome fields into a form
 *  but note that you have to validate and upload the field
 *  data manually
 *
 *  This field will render strings, Dom\Templates and Dom\Renderer\Iface
 *
 * @package Form\Field
 */
class Renderer extends Iface
{
    /**
     * @var \Mod\Renderer|\Mod\Template|string
     */
    protected $renderer = null;


    public function __construct($name, $renderer)
    {
        parent::__construct($name);
        //$this->setLoadable(false);
        $this->setReadonly();
        $this->renderer = $renderer;
    }

    /**
     * Render the widget.
     *
     */
    public function show()
    {
        $t = $this->getTemplate();

        if ($this->renderer instanceof \Dom\RendererInterface) {
            $t->setAttr('field', 'id', $this->getId());
            $this->renderer->show();
            $t->appendTemplate('field', $this->renderer->getTemplate());
        } else if ($this->renderer instanceof \Dom\Template) {
            $t->appendTemplate('field', $this->renderer);
        } else if (is_string($this->renderer)) {
            $t->insertHtml('field', $this->renderer);
        }
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
<div class="tk-Renderer" var="field">
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }


}