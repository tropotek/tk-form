/*
 * Plugin: tkCombobox
 * Version: 1.0
 * Date: 11/05/17
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */

/**
 *
 * <code>
 *   $(document).ready(function() {
 *     // attach the plugin to an element
 *     $('select#element').tkCombobox({'foo': 'bar'});
 *
 *     // call a public method
 *     $('select#element').data('tkCombobox').foo_public_method();
 *
 *     // get the value of a property
 *     $('select#element').data('tkCombobox').settings.foo;
 *   
 *   });
 * </code>
 */
;(function($) {
  var tkCombobox = function(element, options) {
    // plugin vars
    var defaults = {
      foo: 'bar',
      onFoo: function() {}
    };
    var plugin = this;
    var $element = $(element);
    plugin.settings = {};
    var combobox = null;

    // constructor method
    plugin.init = function() {
      plugin.settings = $.extend({}, defaults, options);

      
      combobox = $(
        '<div class="input-group tkCombobox">' +
          '<input type="text" class="form-control" />' +
          '<div class="input-group-btn">' +
            '<button type="button" class="btn btn-default"><span class="caret"></span></button>' +
            '<ul class="dropdown-menu">' +
            '</ul>' +
          '</div>' +
        '</div>'
      );

      var cInput = combobox.find('input');
      var cList = combobox.find('ul');
      var cButton = combobox.find('button');

      cInput.attr('autocomplete', 'off');
      cInput.attr('name', $element.attr('name'));
      cInput.attr('id', $element.attr('id'));
      cInput.val($element.val());
      $element.attr('name', $element.attr('name')+'-org');
      $element.attr('id', $element.attr('id')+'-org');

      $element.before(combobox);
      $element.hide();
      cList.hide();

      
      var optionsList =  $element.find('option');
      for(var i = 0; i < optionsList.length; i++) {
        var el = $('<li><a href="javascript:;"></a></li>');
        el.find('a').text(optionsList.get(i).value);
        cList.append(el);
      }
      cList.find('li a').on('mousedown', function (e) {
        cInput.val($(this).text());
      });
      
      // Show/Hide
      cInput.on('focus', function (e) {
        cList.show();
      }).on('blur', function (e) {
        cList.hide();
      });
      cButton.on('click', function (e) {
        if (cList.is(':visible')) {
          cList.hide();
        } else {
          cList.show();
        }
      });
      
      // See how this performs, close list if click outside.
      $(document).on('mouseup', function (e) {
        if (!combobox.is(e.target) && combobox.has(e.target).length == 0) {
          cList.hide();
        }
      });

      
    };  // END init()

    
    
    
    // private methods
    //var foo_private_method = function() { };

    // public methods
    //plugin.foo_public_method = function() { };

    
    
    // call the "constructor" method
    plugin.init();
  };

  // add the plugin to the jQuery.fn object
  $.fn.tkCombobox = function(options) {
    return this.each(function() {
      if (undefined === $(this).data('tkCombobox')) {
        var plugin = new tkCombobox(this, options);
        $(this).data('tkCombobox', plugin);
      }
    });
  }

})(jQuery);

