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
class Money extends Iface
{

    /**
     *
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name, new \Form\Type\Money());

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
<div class="tk-Money">
  <input type="text" class="money" var="element"/>
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }


}