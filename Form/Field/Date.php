<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 *
 *
 * @package Form\Field
 */
class Date extends Text
{

    /**
     * construct
     *
     * @param string $name
     * @param bool $isNull
     */
    public function __construct($name, $isNull = true)
    {
        parent::__construct($name, new \Form\Type\Date());
        if (!$isNull) {
            $this->setValue(\Tk\Date::create());
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
<div>
<script>
jQuery(function($) {
  $('.Date input').datepicker({
    dateFormat: 'dd/mm/yy'
  });
});
</script>
<input type="text" var="element" />
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }


}