/*
 * Plugin: InputLock
 * Version: 1.0
 * Date: 11/05/17
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 * @source http://stefangabos.ro/jquery/jquery-plugin-boilerplate-revisited/
 */

/**
 * Use this script to create a lockable input text field
 *
 * The user must click the unlock button before they are able to edit its value
 *
 * Requires:
 * <code>
 *   $template->appendJsUrl(\Tk\Uri::create('/src/Tk/Form/Field/jquery.tkAge.js'));
 * </code>
 *
 * <code>
 *   $(document).ready(function() {
 *     // attach the plugin to an element
 *     $('#element').tkAge({'foo': 'bar'});
 *
 *     // call a public method
 *     $('#element').data('tkAge').foo_public_method();
 *
 *     // get the value of a property
 *     $('#element').data('tkAge').settings.foo;
 *   });
 * </code>
 *
 */
;
(function ($) {
  /**
   *
   * @param element Form
   * @param options Object
   */
  var tkAge = function (element, options) {
    // plugin vars
    var defaults = {
      dod: '',                                // Date Of Deth, if set then this is the max age
      igaCss: 'input-group-append',           // (for Bootstrap 4 use: data-iga-css="input-group-btn")
      groupTpl: '<div class="input-group"><div class="tki-iga"><div class="input-group-text" title="Age">Age: 0</div></div></div>'
    };
    var $element = $(element);
    var plugin = this;
    plugin.settings = {};
    var form = null;

    // constructor method
    plugin.init = function () {
      plugin.settings = $.extend({}, defaults, $(element).data(), options);
      form = $(element).closest('form');

      var group = $(plugin.settings.groupTpl);
      group.find('.tki-iga').addClass(plugin.settings.igaCss);

      //$element.parent().prepend(group);
      group.insertBefore($element);

      $element.detach();
      group.prepend($element);


      $element.on('change', function () {
        update(group);
      });
      update(group);


    };  // END init()

    /**
     * @param group
     */
    var update = function(group) {
      var age = 0;
      var now = new Date();
      if (plugin.settings.dod && $(plugin.settings.dod).length) {
        var dodVal = $(plugin.settings.dod).val();
        now = new Date(
          dodVal.substring(6, 10),
          dodVal.substring(3, 5)-1,
          dodVal.substring(0, 2)
        );
      }
      var val = group.find('input').val();
      var dob = new Date(
        val.substring(6, 10),
        val.substring(3, 5)-1,
        val.substring(0, 2)
      );
      // if date <= now() then exit with 0
      if (now > dob) {
        var ageDifMs = now.getTime() - dob.getTime();
        var ageDate = new Date(ageDifMs); // miliseconds from epoch
        age = Math.abs(ageDate.getUTCFullYear() - 1970);
      }
      group.find('.input-group-text').text('Age: ' + age);

    };

    // private methods
    //var foo_private_method = function() { };

    // public methods
    //plugin.foo_public_method = function() { };

    // call the "constructor" method
    plugin.init();
  };

  // add the plugin to the jQuery.fn object
  $.fn.tkAge = function (options) {
    return this.each(function () {
      if (undefined === $(this).data('tkAge')) {
        var plugin = new tkAge(this, options);
        $(this).data('tkAge', plugin);
      }
    });
  };

})(jQuery);
