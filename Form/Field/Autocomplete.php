<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 * Autocomplete Field
 * To Use this field the Com_Web_AjaxController must be installed
 *
 * Once an Ajax object is created pass that to the field on creation:
 *
 * <code>
 *   $form->addField(Form_Field_Autocomplete::create('city', '/ajax/Lst_Ajax_CityAutocomplete'));
 * </code>
 *
 * The returned list from the Ajax object is a JSON object in the form of:
 * <code>
 *  var projects = [
 *    {
 *      value: 'jquery',
 *      label: 'jQuery'
 *    },
 *    {
 *      value: 'jquery-ui',
 *      label: 'jQuery UI'
 *    }
 *  ];
 * </code>
 *
 * So you must use somthing like the following in the Ajax object:
 *
 * <code>
 *   echo json_encode($out);
 * </code>
 *
 *
 *
 * @notice: This can be removed as it is only javscript and can be applied in the theme
 * @package Form\Field
 * @deprecated
 */
class Autocomplete extends Text
{

    /**
     * @var \Tk\Url
     */
    protected $ajaxUrl = null;

    protected $minLength = 2;


    /**
     * Create an instance of this object
     *
     * @param string $name
     * @param string|\Tk\Url $ajaxUrl
     * @param int $minLength
     */
    public function __construct($name, $ajaxUrl, $minLength = 2)
    {
        parent::__construct($name);
        $this->minLength = (int)$minLength;
        $this->ajaxUrl = \Tk\Url::create($ajaxUrl);
        $this->setAutocomplete(false);

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
<style>
.tk-form .ui-autocomplete, .ui-autocomplete li {
  list-style: none;
}
</style>
<script>
jQuery(function($) {
  var url = '{$this->ajaxUrl->toString()}';
  var max = {$this->minLength};

  $('.tk-form .Autocomplete input#{$this->getId()}').autocomplete({
    source: url,
    minLength: max,
    select: function(event, ui) {
        $(this).val(ui.item.label);
        return false;
    }
  });
});
jQuery(function($) {

});
</script>
  <input type="text" var="element" />
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }


}