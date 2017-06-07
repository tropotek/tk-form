
/**
 * Title: tkImagePreview
 * Version 1.0, Jul 14th, 2016
 * by Metal Mick
 *
 * This plugin was created from the code snippet: http://bootsnipp.com/snippets/featured/input-file-popover-preview-image
 *
 * <code>
 *   $(document).ready(function() {
 *     // attach the plugin to an element
 *     $('#element').tkImageInput({'foo': 'bar'});
 *
 *     // call a public method
 *     $('#element').data('tkImageInput').foo_public_method();
 *
 *     // get the value of a property
 *     $('#element').data('tkImageInput').settings.foo;
 *
 *   });
 * </code>
 *
 *
 */
(function($) {
  var tkImageInput = function(element, options) {

    // Default options
    var defaults = {
      dataUrl: '',
      enableDelete: true,

      // Defaults for the tkForm file field
      onInit: function(plugin) {
        $(this).closest('.form-group').find('.tk-file-delete').hide();

        var thumb = $(plugin.settings.thumbTpl);
        plugin.settings.template.find('.input-group-btn').append(thumb);

        var img = thumb.find('img');
        if ($.fn.popover !== undefined) {
          thumb.popover({
            trigger: 'manual',
            html: true,
            //title: '<strong>Preview</strong> ',
            content: '',
            placement: 'top'
          }).css({textAlign: 'center'});
          thumb.hover(
            function (e) {
              $(this).popover('show');
            },
            function (e) {
              $(this).popover('hide');
            }
          ).click(function(e) {
            $(this).popover('show').blur();
          });
        }
        var val = $(this).attr('value');
        if (val) {
          if (isImage(val)) {
            img.attr('src', plugin.settings.dataUrl + val).show();
            thumb.show();
            thumb.attr('data-content', copyImageHtml(img));
          } else {
            thumb.hide();
            thumb.attr('data-content', '');
          }
        }
      },
      onSelect: function(plugin) {
        if (!this.files.length) return;
        $(this).closest('.form-group').find('.tk-file-delete input[type=checkbox]').prop('checked', false);
      },
      onFileLoad: function(plugin, file, e) {
        if (typeof file === 'undefined') return;
        var thumb = plugin.settings.template.find('.tfi-btn-thumb');
        var img = thumb.find('img');
        if (isImage(file.name)) {
          thumb.show();
          img.attr('src', e.target.result);
          thumb.attr('data-content', copyImageHtml(img));
          thumb.removeClass('disabled');
        }

      },
      onDelete: function(plugin) {
        $(this).closest('.form-group').find('.tk-file-delete input[type=checkbox]').prop('checked', true);
        plugin.settings.template.find('.tfi-btn-thumb').hide();
      },
      onError: function(plugin, file, msg) { console.log('onError'); console.log(msg); },


      // Default Templates
      thumbTpl:
        '<button type="button" class="btn btn-default tfi-btn-thumb" title="" style="display: none;">' +
          '<img class="thumb-img" src="javascript:;" alt="" style=""/>&nbsp;' +
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

      $element.tkFileInput(plugin.settings);

    };  /// End plugin.init()

    /**
     *
     * @param filename
     */
    var isImage = function(filename) {
      var ext = getExtension(basename(filename)).toLowerCase();
      switch (ext) {
        case 'jpg':
        case 'jpeg':
        case 'gif':
        case 'png':
          return true;
      }
      return false;
    };

    /**
     *
     * @param path
     * @returns {XML|string}
     */
    var basename = function(path) {
      return path.replace(/\\/g,'/').replace( /.*\//, '' );
    };

    /**
     *
     * @param file
     */
    var getExtension = function(file) {
      var pos = file.lastIndexOf('.');
      if (pos > -1) {
        return file.substring(pos + 1);
      }
      return '';
    };

    /**
     *
     * @param img
     * @returns {*|string}
     */
    var copyImageHtml = function (img) {
      var cpy = $(img).clone();
      //cpy.attr('style', '').css({maxWidth: 250, height: 'auto'});
      return cpy[0].outerHTML;
    };

    // call the "constructor" method
    plugin.init();
  };


  // add the plugin to the jQuery.fn object
  $.fn.tkImageInput = function(options) {
    return this.each(function() {
      if (undefined === $(this).data('tkImageInput') && undefined === $(this).data('tkFileInput')) {
        var plugin = new tkImageInput(this, options);
        $(this).data('tkImageInput', plugin);
      }
    });
  }

})(jQuery);














