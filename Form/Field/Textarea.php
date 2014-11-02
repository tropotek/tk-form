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
class Textarea extends Text
{


    /**
     * __construct
     *
     * @param string $name
     * @param \Form\Type\Iface $type
     */
    public function __construct($name, $type = null)
    {
        parent::__construct($name, $type);
    }

    /**
     * Show
     *
     */
    public function show()
    {
        parent::show();
        $t = $this->getTemplate();
        $fieldValues = $this->getType()->getFieldValues();
        if (isset($fieldValues[$this->name]) && !is_array($fieldValues[$this->name])) {
            $t->insertText('element', $fieldValues[$this->name]);
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
<textarea var="element"></textarea>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }
}