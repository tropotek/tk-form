<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 *  A form checkbox field object, usefull for boolean queries
 *
 * @package Form\Field
 */
class Checkbox extends Iface
{

    public function __construct($name)
    {
        $type = new \Form\Type\Boolean();
        parent::__construct($name, $type);
        $this->clearCssClassList();
    }

    /**
     * Is the value checked
     *
     * @return bool
     */
    public function isChecked()
    {
        $arr = $this->type->getFieldValues();
        //if (isset($arr[$this->name]) && ($arr[$this->name] === $this->name || $arr[$this->name] == 0) ) {
        if (!empty($arr[$this->name])) {
            return true;
        }
        return false;
    }

    /**
     * Render the widget.
     *
     * @return \Dom\Template|string|void
     */
    public function show()
    {
        parent::show();
        $t = $this->getTemplate();
        if ($this->isChecked()) {
            $t->setAttr('element', 'checked', 'checked');
        }
        $t->setAttr('element', 'value', $this->name);
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
<label style="display: block;">
<input type="checkbox" var="element" />
</label>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }



}