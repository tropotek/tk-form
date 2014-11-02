<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 *  A form text field object
 *
 * @package Form\Field
 */
class Hidden extends Iface
{

    /**
     * constructor
     *
     * @param string $name
     * @param \Form\Type\Iface $type
     */
    public function __construct($name, $type = null)
    {
        parent::__construct($name, $type);
        $this->enableFieldWrapper(false);

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
<input type="hidden" var="element" name="" value="" />
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }

}