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
class DateSet extends Iface
{



    /**
     * construct
     *
     * @param string $name
     * @param bool $isNull
     */
    public function __construct($name, $isNull = true)
    {
        parent::__construct($name, new \Form\Type\DateSet());
        if (!$isNull) {
            $this->setValue(\Tk\Date::create());
        }
        $this->addStyle('width', '100px');
        $this->addCssClass('date');
    }




    /**
     * TODO: Check GMap type and see why this is not
     * needed there, probably something to do with the
     * the date being an object and the maps being strings.... Check it out
     *
     *
     * @return bool
     */
    public function isMultiField()
    {
        return true;
    }





    function getFieldName($fieldName = '')
    {
        if ($fieldName) {
            return $this->getName().ucfirst($fieldName);
        }
        return $this->getName();
    }

    function getFieldId($fieldName = '')
    {
        $str = 'fid';
        if ($this->getForm()) {
            $str = $this->getForm()->getId();
        }
        return $str . '_' . $this->getFieldName($fieldName);
    }

    /**
     * Render the widget.
     *
     */
    public function show()
    {
        $t = $this->getTemplate();
        parent::show();

        if (!$this->enabled) {
            $t->setAttr('from', 'disabled', 'disabled');
            $t->setAttr('to', 'disabled', 'disabled');
        }
        if ($this->required && !$this->form->hasTabGroups()) {
            $t->setAttr('from', 'required', 'required');
            $t->setAttr('to', 'required', 'required');
        }
        if ($this->readonly) {
            $t->setAttr('from', 'readonly', 'readonly');
            $t->setAttr('to', 'readonly', 'readonly');
        }
        if (!$this->autocomplete) {
            $t->setAttr('from', 'autocomplete', 'off');
            $t->setAttr('to', 'autocomplete', 'off');
        }

        if ($this->accessKey) {
            $t->setAttr('from', 'accesskey', $this->accessKey);
        }
        if ($this->tabindex > 0) {
            $t->setAttr('from', 'tabindex', $this->tabindex);
        }

        $t->setAttr('from', 'placeholder', 'Date From');
        $t->setAttr('to', 'placeholder', 'Date To');

        //$this->cssList[] = 'tk-element-'.lcfirst(substr(get_class($this), strrpos(get_class($this), '\\')+1));
        foreach ($this->cssList as $v) {
            $t->addClass('from', $v);
            $t->addClass('to', $v);
        }

        $style = '';
        foreach ($this->getStyleList() as $name => $val) {
            $style .= $name . ':'.$val.';';
        }
        if ($style) {
            $t->setAttr('from', 'style', $style);
            $t->setAttr('to', 'style', $style);
        }

        // Element
        $t->setAttr('from', 'name', $this->getFieldName('from'));
        $t->setAttr('to', 'name', $this->getFieldName('to'));
        $t->setAttr('from', 'id', $this->getFieldId('from'));
        $t->setAttr('to', 'id', $this->getFieldId('to'));


        // Element Value
        $fieldValues = $this->getType()->getFieldValues();

        $n = $this->getFieldName().'From';
        if (isset($fieldValues[$n]) && !is_array($fieldValues[$n])) {
            $t->setAttr('from', 'value', $fieldValues[$n]);
        }
        $n = $this->getFieldName().'To';
        if (isset($fieldValues[$n]) && !is_array($fieldValues[$n])) {
            $t->setAttr('to', 'value', $fieldValues[$n]);
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
<div class="dateSet">
<style>
.tk-form .DateSet .dateFrom,
.tk-form .DateSet .dateTo { display: inline-block;}
.tk-form .DateSet .clear { display: inline-block; margin-left: 10px; vertical-align: bottom; }
</style>
<script>
//<![CDATA[
jQuery(function($) {
  $( '.DateSet' ).each(function(k, v) {
    var _this = $(this);
    _this.find('.dateFrom input').datepicker({
      dateFormat: 'dd/mm/yy',
      defaultDate: '+1w',
      changeMonth: false,
      //numberOfMonths: 2,
      //minDate: new Date(),
      onClose: function( selectedDate ) {
        _this.find('.dateTo input').datepicker( 'option', 'minDate', selectedDate );
      }
    });
    _this.find('.dateTo input').datepicker({
      dateFormat: 'dd/mm/yy',
      defaultDate: '+1w',
      changeMonth: false,
      numberOfMonths: 2,
      onClose: function( selectedDate ) {
        _this.find('.dateFrom input').datepicker( 'option', 'maxDate', selectedDate );
      }
    });
    $('.dateSet button').click(function () {
      $(this).parents('.dateSet').find('input').val('');
      return false;
    });
  });
});
//]]>
</script>
  <div class="dateFrom">From <input type="text" class="" var="from" /></div>
  <div class="dateTo">To <input type="text" class="" var="to" /></div>
  <div class="clear">
    <button class="btn btn-xs btn-default">Clear</button>
  </div>
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }

}