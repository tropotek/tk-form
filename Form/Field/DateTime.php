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
class DateTime extends Text
{

    /**
     *
     * @param string $name
     * @param bool $isNull
     */
    public function __construct($name, $isNull = true)
    {
        parent::__construct($name, new \Form\Type\DateTime());
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
<script src="js/jquery.timepicker.js"></script>
<style>

</style>
<script>
jQuery(function($) {
  $('.tk-form .DateTime input').datetimepicker({
      dateFormat: 'dd/mm/yy',
      showTime: true,
      timeFormat: 'hh:mm',
    //timeFormat: 'hh:mm:ss:l',
      stepHour: 1,
      stepMinute: 5,
      hourGrid: 4,
      minuteGrid: 15,
    //showSecond: true,
    //stepSecond: 10,
    //showMillisec: true
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