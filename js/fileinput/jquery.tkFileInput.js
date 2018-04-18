
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
      placeholder: '',
      deleteQueryName: 'del',   // will end up appending to the url ==>  &del={/relative/path.png}

      // Default Templates
      template:
        '<div class="input-group tkFileInput">' +
          '<span class="input-group-btn">' +
            '<div class="btn btn-default tfi-btn-input" title="Select File(s)">' +   // file select button
              '<i class="glyphicon glyphicon-folder-open"></i>' +
            // <!-- where the original input will be placed -->
            // <input id="formEdit_attach" class="form-control tk-fileinput" type="file" multiple="true" data-maxsize="1038090240" name="attach[]" />
            '</div>' +
            // Append other buttons here ...
          '</span>' +
          '<input type="text" class="form-control tfi-input-filename" disabled="disabled" placeholder="Click To Add File.." /> <!-- don`t give a name ==> not send on POST/GET -->' +
        '</div>',
      deleteTpl:
        '<button type="button" class="btn btn-default tfi-btn-del" title="Remove File(s)">' +   // tfi-btn-del button
          '<i class="glyphicon glyphicon-trash"></i>' +
        '</button>',

      // Defaults for the tkForm file field
      onInit: function(plugin) {
        $(this).closest('.form-group').find('.tk-file-delete').hide();
      },
      onSelect: function(plugin) {
        if (!this.files.length) return;
        $(this).closest('.form-group').find('.tk-file-delete input[type=checkbox]').prop('checked', false);
      },
      onFileLoad: function(plugin, file) { },
      onDelete: function(plugin) {
        $(this).closest('.form-group').find('.tk-file-delete input[type=checkbox]').prop('checked', true);
      },
      onError: function(plugin, msg) {
        var div = $('<span class="help-block alert alert-danger"><i class="glyphicon glyphicon-ban-circle"></i> '+msg+'</span>').hide();
        $(this).closest('.input-group').parent().append(div);
        div.fadeIn(500);
        setTimeout(function () {
          div.fadeOut(500, function () { $(this).remove(); });
        }, 7000);
      }
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
      if (plugin.settings.placeholder) {
        template.find('input.tfi-input-filename').attr('placeholder', plugin.settings.placeholder);
      }
      if (plugin.settings.enableDelete)
        template.find('.input-group-btn').append(plugin.settings.deleteTpl);

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
        template.find('input.tfi-input-filename').val($element.attr('value').replace(/\\/g, '/').replace(/.*\//, ''));
      } else {
        template.find('.tfi-btn-del').hide();
      }

      $element.on('change', function(e) {
        if (this.files.length)
          template.find('.tfi-btn-del').show();

        var name = '';
        for(var i = 0; i < this.files.length; i++) {
          var file = this.files[i];
          if (typeof file !== 'undefined') {
            name = file.name;
            if ($element.attr('data-maxsize') && file.size) {
              var maxSize = parseInt($element.attr('data-maxsize'), 10);
              if (file.size > maxSize) {
                var msg = 'Error: <b>`' + name + '`</b> <i>[' + formatBytes(file.size) + ']</i> file size exceeds allowed maximum of <b>' + formatBytes($element.attr('data-maxsize')) + '</b>';
                plugin.settings.onError.apply(element, [plugin, msg]);
                file.error = msg;
                continue;
              }
            }
            plugin.settings.onFileLoad.apply(element, [plugin,  file]);
          }
        }
        $parent.find('.tfi-input-filename').val(name);
        plugin.settings.onSelect.apply(element, [plugin]);
      });

      template.find('.tfi-btn-del').on('click', function(e) {
        var inputGroup = $(this).closest('.input-group');
        inputGroup.find('.tfi-input-filename').val('');
        $element.attr('value', '');
        plugin.settings.onDelete.apply(element, [plugin]);
        $(this).hide();
      });

      plugin.settings.onInit.apply(element, [plugin]);
    };  /// End plugin.init()

    /**
     *
     * @param bytes
     * @param decimals
     * @returns {*}
     */
    var formatBytes = function (bytes, decimals) {
      if(bytes === 0) return '0 Byte';
      var k = 1024; // or 1024 for binary
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
        $(this).data('tkInput', plugin);
      }
    });
  }

})(jQuery);




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
      // Default Templates
      thumbTpl:
      '<button type="button" class="btn btn-default tfi-btn-thumb" title="" style="display: none;">' +
      '<img class="thumb-img" src="javascript:;" alt="Preview"/>&nbsp;' +
      '</button>',

      // Defaults for the tkForm file field
      onInit: function(plugin) {
        $(this).closest('.form-group').find('.tk-file-delete').hide();
        var thumb = $(plugin.settings.thumbTpl);
        plugin.settings.template.find('.input-group-btn').append(thumb);

        if ($(this).parents('.form-group-sm').length || $(this).hasClass('input-sm')) {
          thumb.removeClass('btn-lg').removeClass('btn-xs').addClass('btn-sm');
        } else if ($(this).parents('.form-group-lg').length || $(this).hasClass('input-lg')) {
          thumb.removeClass('btn-sm').removeClass('btn-xs').addClass('btn-lg');
        }

        var img = thumb.find('img');
        if ($.fn.popover !== undefined) {
          thumb.popover({
            trigger: 'manual',
            html: true,
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
        var thumb = plugin.settings.template.find('.tfi-btn-thumb');
        thumb.hide();
        $(this).closest('.form-group').find('.tk-file-delete input[type=checkbox]').prop('checked', false);
      },
      onFileLoad: function(plugin, file) {
        if (typeof file === 'undefined') return;
        var thumb = plugin.settings.template.find('.tfi-btn-thumb');
        var img = thumb.find('img');
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.file = file;
        reader.onload = function (e) {
          if (isImage(this.file.name)) {
            thumb.show();
            img.attr('src', this.result);
            thumb.attr('data-content', copyImageHtml(img));
            thumb.removeClass('disabled');
          }
        };
      },
      onDelete: function(plugin) {
        $(this).closest('.form-group').find('.tk-file-delete input[type=checkbox]').prop('checked', true);
        plugin.settings.template.find('.tfi-btn-thumb').hide();
      }
    };

    var plugin = this;
    plugin.settings = {};
    var $element = $(element);
    var $parent = $element.parent();    // Get the containing div

    /**
     * constructor
     */
    plugin.init = function() {
      plugin.settings = $.extend({}, defaults, options);
      $element.tkFileInput(plugin.settings);

    };  /// End plugin.init()

    /**
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
     * @param path
     * @returns {XML|string}
     */
    var basename = function(path) {
      return path.replace(/\\/g,'/').replace( /.*\//, '' );
    };

    /**
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
      if (undefined === $(this).data('tkImageInput') && undefined === $(this).data('tkInput')) {
        var plugin = new tkImageInput(this, options);
        $(this).data('tkImageInput', plugin);
        $(this).data('tkInput', plugin);
      }
    });
  }

})(jQuery);



/**
 * Title: tkMultiInput
 * Version 1.0, Jul 14th, 2016
 * by Metal Mick
 *
 * This plugin was created from the code snippet: http://bootsnipp.com/snippets/featured/input-file-popover-preview-image
 *
 * <code>
 *   $(document).ready(function() {
 *     // attach the plugin to an element
 *     $('#element').tkMultiInput({'foo': 'bar'});
 *
 *     // call a public method
 *     $('#element').data('tkMultiInput').foo_public_method();
 *
 *     // get the value of a property
 *     $('#element').data('tkMultiInput').settings.foo;
 *
 *   });
 * </code>
 */
(function($) {
  var tkMultiInput = function(element, options) {

    // Default options
    var defaults = {
      dataUrl: '',
      enableDelete: false,
      multipleSelect: null,
      serverConfirm: 'Are you sure you want to delete this file from the server?',
      localConfirm: '',
      cloneid: 0,
      
      tableTpl : 
        '<table class="table table-striped tfi-table"></table>',
      
      rowTpl:'<tr class="tfi-row">'+
        '<td class="text-right"><a href="#" title="View File" class="btn btn-xs btn-default tfi-btn-view"><i class="fa fa-eye"></i></a></td>'+
        '<td class="key"><i class="tfi-icon fa fa-file-o" title="Archive"></i>&nbsp; <a href="#" target="_blank" class="tfi-filename">someFileName.tgz</a></td>'+
        '<td class="tfi-file-size"><a href="#" title="Delete" class="btn btn-xs btn-default tfi-btn-delete"><i class="fa fa-trash"></i></a> &nbsp; <span>673Kb</span></td>'+
      '</tr>',

      // Defaults for the tkForm file field
      onInit: function(plugin) {
        $(this).closest('.form-group').find('.tk-file-delete').hide();
        if (plugin.settings.multipleSelect === null) {
          plugin.settings.multipleSelect = ($element.attr('name').indexOf('[]') > -1);
        }

        if (!plugin.settings.multipleSelect)
          $element.removeAttr('multiple');
        plugin.settings.template.find('.tfi-input-filename').remove();
        plugin.settings.template.find('.tfi-btn-input i').after('<span class="tfi-label">Select Files</span>');
      },

      onSelect: function(plugin) {
        if (!this.files.length) return;
        var filename = plugin.settings.template.find('.tfi-input-filename');
        filename.val('');

        // Clone input field
        var clone = $(this).clone(this);
        plugin.settings.cloneid = plugin.settings.cloneid+1;
        clone.removeAttr('id').removeAttr('class').addClass('tfi-clone').removeAttr('value').hide();
        if (!clone.attr('name').endsWith('[]')) clone.setAttr('name', clone.attr('name')+'[]');
        clone.attr('data-clone-id', plugin.settings.cloneid);
        $(this).closest('.form-group').append(clone);
        
        
        // create new rows
        var files = this.files;
        for(var i = 0; i < files.length; i++) {
          var file = files[i];
          console.log(file);
          if (file.error) {
              //console.log(maxSize, file);
            continue;
          }


          var row = $(plugin.settings.rowTpl);
          row.attr('data-clone-id', plugin.settings.cloneid).data('file', file);
          row.find('.tfi-btn-delete').attr('href', 'javascript:;').on('click',
            function (e) {
              $(this).blur();
              if (!plugin.settings.localConfirm || confirm(plugin.settings.localConfirm)) {
                return plugin.settings.onDelete.apply($(this).closest('tr'), [plugin]);
              }
              return false;
            });
          row.find('.tfi-icon').removeClass('fa-file-o').addClass(getIcon(file.name));
          row.addClass('tfi-new');
          row.find('.tfi-filename').attr('href', 'javascript:;').removeAttr('target').removeAttr('href').addClass('disabled').text(basename(file.name));
          row.find('.tfi-btn-view').attr('href', 'javascript:;').removeAttr('target').removeAttr('href').addClass('disabled');
          row.find('.tfi-file-size span').text(formatBytes(file.size));
          
          plugin.settings.table.append(row);
        }
      },
      onFileLoad: function(plugin, file) { },
      onUrlLoad: function(plugin, uri) { },
      onDelete: function(plugin) {
        // remove row from table
        $(this).addClass('active').find('a, button, input, .btn').attr('disabled', 'disabled').addClass('disabled').on('click', function () {return false;});
        if ($(this).hasClass('tfi-new')) {
          $(this).closest('.form-group').find('[data-clone-id=' + $(this).attr('data-clone-id') + ']').remove();
        } else {
          $.ajax({
            url : $(this).find('.tfi-btn-delete').attr('href'),
            method: 'GET',
            context: this
          }).done(function(data) {
            $(this).remove();
          });
        }
        return false;
      }
      
    };


    var plugin = this;
    plugin.settings = { };
    var $element = $(element);
    var $parent = $element.parent();    // Get the containing div

    /**
     * constructor
     */
    plugin.init = function() {
      plugin.settings = $.extend({}, defaults, options);
      
      var table = plugin.settings.table = $(plugin.settings.tableTpl);
      $element.tkFileInput(plugin.settings);
      $element.closest('.input-group').parent().append(table);

      $element.closest('form').on('submit', function () {
        element.disabled = true;    // Stops duplicate file uploads 
      });

      // It is expected that the files will be a json string array of urls in the input value
      var list = [];
      if ($element.attr('value'))
        list = JSON.parse($element.attr('value'));
      
      // Setup initial field value files
      if (list !== undefined && Array.isArray(list)) {
        for(var i = 0; i< list.length; i++) {
          var filename = plugin.settings.dataUrl + list[i];
          $.ajax({
            type: 'HEAD',
            url: filename,
            complete: function(xhr) {
              if (xhr.status !== 200) return;
              this.xhr = xhr;
              var row = $(plugin.settings.rowTpl);
              row.data('filename', this.url);
              row.find('.tfi-btn-delete').attr('href', setQueryParameter(document.location.href, 'del', basepath(this.url))).on('click',
              function (e) {
                $(this).blur();
                if (!plugin.settings.serverConfirm || confirm(plugin.settings.serverConfirm)) {
                  return plugin.settings.onDelete.apply($(this).closest('tr'), [plugin]);
                }
                return false;
              });
              row.find('.tfi-icon').removeClass('fa-file-o').addClass(getIcon(this.url));
              row.find('.tfi-filename').addClass('ui-lightbox').attr('href', this.url).text(basename(this.url));
              row.find('.tfi-btn-view').addClass('ui-lightbox').attr('href', this.url);
              if ($.fn.magnificPopup && isImage(this.url)) {
                row.find('.tfi-filename').magnificPopup({type: 'image'})
              }
              row.find('.tfi-file-size span').text(formatBytes(xhr.getResponseHeader('Content-Length')));
              table.append(row);

              plugin.settings.onUrlLoad.apply(element, [plugin,  this]);
            }
          });
        }
      }

    };  /// End plugin.init()

    var getIcon = function (url) {
      var ext = getExtension(url);
      switch(ext) {
        case 'zip': case 'gz': case 'tar': case 'gtz': case 'rar': case '7zip': case 'jar': case 'pkg': case 'deb':
          return 'fa-file-archive-o';
        case 'h': case 'c': case 'php': case 'js': case 'css': case 'less': case 'txt': case 'xml': case 'xslt': case 'json':
          return 'fa-file-code-o';
        case 'ods': case 'sdc': case 'sxc': case 'xls': case 'xlsm': case 'xlsx': case 'csv':
          return 'fa-file-excel-o';
        case 'bmp': case 'emf': case 'gif': case 'ico': case 'icon': case 'jpeg': case 'jpg': case 'pcx': case 'pic': case 'png': case 'psd': case 'raw': case 'tga': case 'tif': case 'tiff': case 'swf': case 'drw': case 'svg': case 'svgz': case 'ai':
          return 'fa-file-image-o';
        case 'aiff': case 'cda': case 'dvf': case 'flac': case 'm4a': case 'm4b': case 'midi': case 'mp3': case 'ogg': case 'pcm': case 'snd': case 'wav':
          return 'fa-file-audio-o';
        case 'avi': case 'mov': case 'mp4': case 'mpg': case 'mpeg': case 'mkv': case 'ogv': case 'flv': case 'webm': case 'wmv': case 'asx':
          return 'fa-file-video-o';
        case 'pdf':
          return 'fa-file-pdf-o';
        case 'ppt': case 'pot': case 'potx': case 'pps': case 'ppsx': case 'pptx': case 'pptm':
          return 'fa-file-powerpoint-o';
        case 'doc': case 'docm': case 'dotm': case 'dotx': case 'docx': case 'dot': case 'wri': case 'wps':
          return 'fa-file-word-o';
      }
      return 'fa-file-o';
    };

    /**
     * 
     * @param uri
     * @param key
     * @param value
     * @returns {string}
     */
    var setQueryParameter = function(uri, key, value) {
      var re = new RegExp("([?&])("+ key + "=)[^&#]*", "g");
      if (uri.match(re))
        return uri.replace(re, '$1$2' + value);

      // need to add parameter to URI
      var paramString = (uri.indexOf('?') < 0 ? "?" : "&") + encodeURIComponent(key) + "=" + encodeURIComponent(value);
      var hashIndex = uri.indexOf('#');
      if (hashIndex < 0)
        return uri + paramString;
      else
        return uri.substring(0, hashIndex) + paramString + uri.substring(hashIndex);
    };

    /**
     * @param path
     * @returns {XML|string}
     */
    var basename = function(path) {
      return path.replace(/\\/g,'/').replace( /.*\//, '' );
    };

    /**
     * @param path
     */
    var basepath = function(path) {
      return path.replace(plugin.settings.dataUrl,'');
    };

    /**
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
     * @param bytes
     * @param decimals
     * @returns {*}
     */
    var formatBytes = function (bytes, decimals) {
      if(bytes === 0 || isNaN(bytes) || bytes === null) return '0 Byte';
      var k = 1024; // or 1024 for binary
      var dm = decimals + 1 || 2;
      var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
      var i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    };

    // call the "constructor" method
    plugin.init();
  };

  // add the plugin to the jQuery.fn object
  $.fn.tkMultiInput = function(options) {
    return this.each(function() {
      if (undefined === $(this).data('tkMultiInput') && undefined === $(this).data('tkInput')) {
        var plugin = new tkMultiInput(this, options);
        $(this).data('tkMultiInput', plugin);
        $(this).data('tkInput', plugin);
      }
    });
  }

})(jQuery);















