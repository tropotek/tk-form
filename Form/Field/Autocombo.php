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
 *  var $projects = [
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
 *   echo json_encode($projects);
 * </code>
 *
 *
 *
 * @notice: This can be removed as it is only javscript and can be applied in the theme
 * @package Form\Field
 * @deprecated
 */
class Autocombo extends Text
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
    public function __construct($name, $ajaxUrl, $minLength = 1)
    {
        parent::__construct($name);
        $this->minLength = $minLength;
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

    $.widget( 'ui.combobox', {
        _create: function() {
            var self = this,
                input = $(this.element);

            input.autocomplete({
                delay: 0,
                minLength: max,
                source: self.options.url,
                select: function( event, ui ) {
                    input.val(ui.item.value);
                    return false;
                },
                change: function( event, ui ) {
                    input.val(input.val());
                }
            }).addClass( 'ui-widget ui-widget-content ui-corner-left' );

            input.data( 'autocomplete' )._renderItem = function( ul, item ) {
                return $( '<li></li>' )
                    .data( 'item.autocomplete', item )
                    .append( '<a>' + item.label + '</a>' )
                    .appendTo( ul );
            };

            this.button = $( '<button type="button">&nbsp;</button>' )
                .insertAfter( input )
                .attr( 'tabIndex', -1 )
                .attr( 'title', 'Show All Items' )
                .button({
                    icons: {
                        primary: 'ui-icon-carat-1-s'
                    },
                    text: false
                })
                .removeClass( "ui-corner-all" )
                .addClass( 'ui-corner-right ui-button-icon' )
                .click(function() {
                    // close if already visible
                    if ( input.autocomplete( 'widget' ).is( ':visible' ) ) {
                        input.autocomplete( 'close' );
                        return;
                    }

                    // work around a bug (likely same cause as #5265)
                    $( this ).blur();

                    // pass empty string as value to search for, displaying all results
                    input.autocomplete('search', '');
                    input.focus();
                });
        },

        destroy: function() {
            this.button.remove();
            $.Widget.prototype.destroy.call( this );
        }
    });
});

jQuery(function($) {
    $('.tk-form .AutoCombo input').combobox({ url : url });
});
</script>
  <input type="text" var="element" />
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }


}