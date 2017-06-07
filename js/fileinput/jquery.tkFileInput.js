
/**
 * Title: tkFileInput
 * Version 1.0, Jul 14th, 2016
 * by Metal Mick
 *
 * This plugin was created from the code snippet: http://bootsnipp.com/snippets/featured/input-file-popover-preview-image
 *
 * <code>
 *   $(document).ready(function() {
 *     // attach the plugin to an element
 *     $('#element').tkFileInput({'foo': 'bar'});
 *
 *     // call a public method
 *     $('#element').data('tkFileInput').foo_public_method();
 *
 *     // get the value of a property
 *     $('#element').data('tkFileInput').settings.foo;
 *
 *   });
 * </code>
 *
 */
;(function($) {
  var tkFileInput = function(element, options) {

    // Default options
    var defaults = {
      enableDelete: true,
      // Events.       //  $(this) = $element
      // onInit: function(plugin) { },
      // onSelect: function(plugin, files) { },
      onFileLoad: function(plugin, file, event) { },
      // onDelete: function(plugin) { },
      // onError: function(plugin, file, msg) { },

      // Defaults for the tkForm file field
      onInit: function(plugin) {
        $(this).closest('.form-group').find('.tk-file-delete').hide();
      },
      onSelect: function(plugin) {
        if (!this.files.length) return;
        $(this).closest('.form-group').find('.tk-file-delete input[type=checkbox]').prop('checked', false);
      },
      onDelete: function(plugin) {
        $(this).closest('.form-group').find('.tk-file-delete input[type=checkbox]').prop('checked', true);
      },
      onError: function(plugin, file, msg) { console.log('onError'); console.log(msg); },

      // Default Templates
      template:
        '<div class="input-group tkFileInput">' +
          '<span class="input-group-btn">' +
            '<div class="btn btn-default tfi-btn-input" title="Select File(s)">' +   // file select button
              '<span class="glyphicon glyphicon-folder-open"></span>' +
            // <!-- where the original input will be placed -->
            // <input id="formEdit_attach" class="form-control tk-fileinput" type="file" multiple="true" data-maxsize="1038090240" name="attach[]" />
            '</div>' +
            // Append other buttons here ...
          '</span>' +
          '<input type="text" class="form-control tfi-filename" disabled="disabled"> <!-- don`t give a name ==> not send on POST/GET -->' +
        '</div>',
      deleteTpl:
        '<button type="button" class="btn btn-default tfi-btn-del" title="Remove File(s)">' +   // tfi-btn-del button
          '<span class="glyphicon glyphicon-trash"></span>' +
        '</button>'
    };
    var plugin = this;
    plugin.settings = {};
    var $element = $(element);
    var $parent = $element.parent();    // Get the containing div

    /**
     * constructor
     *
     */
    plugin.init = function() {
      plugin.settings = $.extend({}, defaults, options);

      // ------------------ INIT ----------------------
      var template = plugin.settings.template = $(plugin.settings.template);

      if (plugin.settings.enableDelete)
        template.find('.input-group-btn').append(plugin.settings.deleteTpl);

      plugin.settings.onInit.apply(element, [plugin]);

      if ($element.parents('.form-group-sm').length || $element.hasClass('input-sm')) {
        template.find('.btn').removeClass('btn-lg').removeClass('btn-xs').addClass('btn-sm');
      } else if ($element.parents('.form-group-lg').length || $element.hasClass('input-lg')) {
        template.find('.btn').removeClass('btn-sm').removeClass('btn-xs').addClass('btn-lg');
      }

      $element.detach();
      $parent.prepend(template);
      template.find('.tfi-btn-input').append($element);

      if ($element.attr('value')) {
        template.find('.tfi-btn-del').show();
        template.find('input.tfi-filename').val($element.attr('value').replace(/\\/g, '/').replace(/.*\//, ''));
      } else {
        template.find('.tfi-btn-del').hide();
      }

      $element.on('change', function(e) {
        if (this.files.length)
          template.find('.tfi-btn-del').show();

        plugin.settings.onSelect.apply(element, [plugin]);

        var name = '';
        for(var i = 0; i < this.files.length; i++) {
          file = this.files[i];
          if (typeof file !== 'undefined') {
            name = file.name;
            var reader = new FileReader();
            reader.onload = function (e) {
              plugin.settings.onFileLoad.apply(element, [plugin, file, e]);
              if ($element.attr('data-maxsize') && file.size) {
                var maxSize = parseInt($element.attr('data-maxsize'), 10);
                if (file.size > maxSize) {
                  var msg = 'File is to large for upload, please check your file size is below ' + formatBytes($element.attr('data-maxsize'));
                  plugin.settings.onError.apply(element, [plugin, file, msg]);
                }
              }
            };
            reader.readAsDataURL(file);
          }
        }
        $parent.find('.tfi-filename').val(name);
      });

      template.find('.tfi-btn-del').on('click', function(e) {
        var inputGroup = $(this).closest('.input-group');
        inputGroup.find('.tfi-filename').val('');
        $element.attr('value', '');
        plugin.settings.onDelete.apply(element, [plugin]);
        $(this).hide();
      });

    };  /// End plugin.init()

    /**
     *
     * @param bytes
     * @param decimals
     * @returns {*}
     */
    var formatBytes = function (bytes,decimals) {
      if(bytes === 0) return '0 Byte';
      var k = 1000; // or 1024 for binary
      var dm = decimals + 1 || 3;
      var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
      var i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    };

    // call the "constructor" method
    plugin.init();
  };


  // add the plugin to the jQuery.fn object
  $.fn.tkFileInput = function(options) {
    return this.each(function() {
      if (undefined === $(this).data('tkFileInput')) {
        var plugin = new tkFileInput(this, options);
        $(this).data('tkFileInput', plugin);
      }
    });
  }

})(jQuery);














