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
;(function ($) {
  let tkFileInput = function (element, options) {

    // Default options
    let defaults = {
      enableDelete: true,
      placeholder: '',
      deleteQueryName: 'del',   // will end up appending to the url ==>  &del={/relative/path.png}
      isBootstrap4: false,

      // Default Templates
      template:
      '<div class="input-group tkFileInput">' +
      '  <span class="input-group-btn input-group-prepend">' +
      '    <div class="btn btn-success tfi-btn-input" title="Select File(s)">' +   // file select button
      '      <i class="fa fa-folder-open-o"></i>' +
             // <!-- where the original input will be placed -->
             // <input id="formEdit_attach" class="form-control tk-fileinput" type="file" multiple="true" data-maxsize="1038090240" name="attach[]" />
      '    </div>' +
         // Append other buttons here ...
      '  </span>' +
      '  <input type="text" class="form-control tfi-input-filename" disabled="disabled" placeholder="Click To Add File.." />' +
        //  <!-- don`t give a name ==> not sent on POST/GET -->
      '</div>',
      deleteTpl:
      '<button type="button" class="btn btn-default tfi-btn-del" title="Remove File(s)">' +   // tfi-btn-del button
      '<i class="fa fa-trash-o"></i>' +
      '</button>',

      // Defaults for the tkForm file field
      onInit: function (plugin) {
        $(this).closest('.form-group').find('.tk-file-delete').hide();
      },
      onSelect: function (plugin) {
        if (!this.files.length) return;
        $(this).closest('.form-group').find('.tk-file-delete input[type=checkbox]').prop('checked', false);
      },
      onFileLoad: function (plugin, file) {
      },
      onDelete: function (plugin) {
        $(this).closest('.form-group').find('.tk-file-delete input[type=checkbox]').prop('checked', true);
      },
      onError: function (plugin, msg) {
        let div = $('<span class="help-block alert alert-danger"><i class="fa fa-ban"></i> ' + msg + '</span>').hide();
        $(this).closest('.input-group').parent().append(div);
        div.fadeIn(500);
        setTimeout(function () {
          div.fadeOut(500, function () {
            $(this).remove();
          });
        }, 7000);
      }
    };
    let plugin = this;
    plugin.settings = {};
    let $element = $(element);
    let $parent = $element.parent();    // Get the containing div

    /**
     * constructor
     */
    plugin.init = function () {
      plugin.settings = $.extend({}, defaults, $element.data(), options);
      //plugin.settings = $.extend({}, defaults, options);

      // ------------------ INIT ----------------------
      let template = plugin.settings.template = $(plugin.settings.template);

      if (plugin.settings.isBootstrap4) {
        template.find('.input-group-prepend').removeClass('input-group-btn');
      } else {
        template.find('.input-group-btn').removeClass('input-group-prepend');
      }

      if (plugin.settings.placeholder) {
        template.find('input.tfi-input-filename').attr('placeholder', plugin.settings.placeholder);
      }
      if (plugin.settings.enableDelete) {
        template.find('.input-group-btn').append(plugin.settings.deleteTpl);
      }

      // if ($element.parents('.form-group-sm').length || $element.hasClass('input-sm')) {
      //   template.find('.btn').removeClass('btn-lg').removeClass('btn-xs').addClass('btn-sm');
      // } else if ($element.parents('.form-group-lg').length || $element.hasClass('input-lg')) {
      //   template.find('.btn').removeClass('btn-sm').removeClass('btn-xs').addClass('btn-lg');
      // }

      $element.detach();
      $parent.prepend(template);
      template.find('.tfi-btn-input').append($element);

      if ($element.attr('value')) {
        template.find('.tfi-btn-del').show();
        template.find('input.tfi-input-filename').val($element.attr('value').replace(/\\/g, '/').replace(/.*\//, ''));
      } else {
        template.find('.tfi-btn-del').hide();
      }

      $parent.on('change', 'input[type=file]', function (e) {
        if (this.files.length) {
          template.find('.tfi-btn-del').show();
        }
        let name = '';
        for (let i = 0; i < this.files.length; i++) {
          let file = this.files[i];
          if (typeof file !== 'undefined') {
            name = file.name;
            if ($(this).attr('data-maxsize') && file.size) {
              let maxSize = parseInt($(this).attr('data-maxsize'), 10);
              if (file.size > maxSize) {
                let msg = 'Error: <b>`' + name + '`</b> <i>[' + formatBytes(file.size) + ']</i> file size exceeds allowed maximum of <b>' +
                  formatBytes($element.attr('data-maxsize')) + '</b>';
                plugin.settings.onError.apply(this, [plugin, msg]);
                file.error = msg;
                continue;
              }
            }
            plugin.settings.onFileLoad.apply(this, [plugin, file]);
          }
        }
        $parent.find('.tfi-input-filename').val(name);
        plugin.settings.onSelect.apply(this, [plugin]);
      });

      template.find('.tfi-btn-del').on('click', function (e) {
        let inputGroup = $(this).closest('.input-group');
        inputGroup.find('.tfi-input-filename').val('');
        inputGroup.find('.tfi-btn-input input[type=file]').attr('value', '');
        plugin.settings.onDelete.apply(inputGroup.find('.tfi-btn-input input[type=file]'), [plugin]);
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
    let formatBytes = function (bytes, decimals) {
      if (bytes === 0) return '0 Byte';
      let k = 1024; // or 1024 for binary
      let dm = decimals + 1 || 3;
      let sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
      let i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    };

    // call the "constructor" method
    plugin.init();
  };

  // add the plugin to the jQuery.fn object
  $.fn.tkFileInput = function (options) {
    return this.each(function () {
      if (undefined === $(this).data('tkFileInput')) {
        let plugin = new tkFileInput(this, options);
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
(function ($) {
  let tkImageInput = function (element, options) {

    // Default options
    let defaults = {
      dataUrl: '',
      enableDelete: true,
      // Default Templates
      thumbTpl:
      '<button type="button" class="btn btn-default tfi-btn-thumb" title="" style="display: none;">' +
      '<img class="thumb-img img-fluid" src="javascript:;" alt="Preview"/>&nbsp;' +
      '</button>',

      // Defaults for the tkForm file field
      onInit: function (plugin) {
        $(this).closest('.form-group').find('.tk-file-delete').hide();
        let thumb = $(plugin.settings.thumbTpl);
        plugin.settings.template.find('.input-group-btn, .input-group-prepend').append(thumb);

        if ($(this).parents('.form-group-sm').length || $(this).hasClass('input-sm')) {
          thumb.removeClass('btn-lg').removeClass('btn-xs').addClass('btn-sm');
        } else if ($(this).parents('.form-group-lg').length || $(this).hasClass('input-lg')) {
          thumb.removeClass('btn-sm').removeClass('btn-xs').addClass('btn-lg');
        }

        let img = thumb.find('img');
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
          ).click(function (e) {
            $(this).popover('show').blur();
          });
        }
        let val = $(this).attr('value');
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
      onSelect: function (plugin) {
        if (!this.files.length) return;
        let thumb = plugin.settings.template.find('.tfi-btn-thumb');
        thumb.hide();
        $(this).closest('.form-group').find('.tk-file-delete input[type=checkbox]').prop('checked', false);
      },
      onFileLoad: function (plugin, file) {
        if (typeof file === 'undefined') return;
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.file = file;
        reader.onload = function (e) {
          if (isImage(this.file.name)) {
            let thumb = plugin.settings.template.find('.tfi-btn-thumb');
            let img = thumb.find('img');
            thumb.show();
            img.attr('src', this.result);
            thumb.attr('data-content', copyImageHtml(img));
            thumb.removeClass('disabled');
          }
        };
      },
      onDelete: function (plugin) {
        $(this).closest('.form-group').find('.tk-file-delete input[type=checkbox]').prop('checked', true);
        plugin.settings.template.find('.tfi-btn-thumb').hide();
      }
    };

    let plugin = this;
    plugin.settings = {};
    let $element = $(element);
    let $parent = $element.parent();    // Get the containing div

    /**
     * constructor
     */
    plugin.init = function () {
      plugin.settings = $.extend({}, defaults, $element.data(), options);
      //plugin.settings = $.extend({}, defaults, options);
      $element.tkFileInput(plugin.settings);

    };  /// End plugin.init()

    /**
     * @param filename
     */
    let isImage = function (filename) {
      let ext = getExtension(basename(filename)).toLowerCase();
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
    let basename = function (path) {
      return path.replace(/\\/g, '/').replace(/.*\//, '');
    };

    /**
     * @param file
     */
    let getExtension = function (file) {
      let pos = file.lastIndexOf('.');
      if (pos > -1) {
        return file.substring(pos + 1);
      }
      return '';
    };

    /**
     * @param img
     * @returns {*|string}
     */
    let copyImageHtml = function (img) {
      let cpy = $(img).clone();
      //cpy.attr('style', '').css({maxWidth: 250, height: 'auto'});
      if (cpy.length)
        return cpy[0].outerHTML;
      return '';
    };

    // call the "constructor" method
    plugin.init();
  };

  // add the plugin to the jQuery.fn object
  $.fn.tkImageInput = function (options) {
    return this.each(function () {
      if (undefined === $(this).data('tkImageInput') && undefined === $(this).data('tkInput')) {
        let plugin = new tkImageInput(this, options);
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
(function ($) {
  let tkMultiInput = function (element, options) {

    // Default options
    let defaults = {
      dataUrl: '',
      enableDelete: false,
      serverConfirm: 'Are you sure you want to delete this file from the server?',
      localConfirm: '',
      enableSelect: false,
      selectTitle: 'Select/Unselect this file',
      multipleSelect: null,   // deprecated
      showThumb: false,
      maxFile: 5,
      thumbHeight: 32,
      // This settings deal with the existing file json objects
      propId: 'id',           // (optional) The property name for an object ID, if non then the propName value will be used for delete requests
      propPath: 'path',       // The property name for the value to be use as the filename
      value: [],              // Array of files that are already uploaded

      tableTpl: '<table class="table table-striped tfi-table"></table>',
      rowTpl: '<tr class="tfi-row" style="vertical-align: top;">' +
        '<td class="select text-right hide" title="Select/Unselect this file"><input type="checkbox" name="selected[]" value="" /></td>' +
        '<td class="download text-right hide"><a href="#" title="Download" class="btn btn-xs btn-default tfi-btn-view" target="_blank"><i class="fa fa-download"></i></a></td>' +
        '<td class="key"><i class="tfi-icon fa fa-file-o" title="Archive"></i>&nbsp; <a href="#" target="_blank" class="tfi-filename">someFileName.tgz</a></td>' +
        '<td class="tfi-file-size"><a href="#" title="Delete" class="btn btn-xs btn-default tfi-btn-delete"><i class="fa fa-trash"></i></a> &nbsp; <span>673Kb</span></td>' +
      '</tr>',

      // Defaults for the tkForm file field
      onInit: function (plugin) {
        $(this).closest('.form-group').find('.tk-file-delete').hide();
        if (plugin.settings.multipleSelect === null) {
          plugin.settings.multipleSelect = ($(this).attr('name').indexOf('[]') > -1);
        }

        $('.tfi-input-filename', plugin.settings.template).remove();
        $('.tfi-btn-input i', plugin.settings.template).after('<span class="tfi-label">Select Files</span>');
      },

      // Function called when new files are selected to be uploaded
      onSelect: function (plugin) {
        if (!this.files.length) return;

        let formGroup = $(this).closest('.form-group');
        let filename = $('.tfi-input-filename', plugin.settings.template);
        filename.val('');
        let files = this.files;
        let file = null;

        if (plugin.settings.maxFiles) {
          if (($('.tfi-row', formGroup).length + files.length) > plugin.settings.maxFiles) {
            alert("You can only upload a maximum of "+plugin.settings.maxFiles+" files per form submission");
            return false;
          }
        }

        // create new rows
        for (let i = 0; i < files.length; i++) {
          file = files[i];
          if (file.error) {
            console.error(file);
            continue;
          }

          let row = $(plugin.settings.rowTpl);
          row.data('file', file);
          row.attr('data-file-name', file.name);
          $('.tfi-btn-delete', row).attr('href', 'javascript:;').on('click',
            function (e) {
              if (!plugin.settings.localConfirm || confirm(plugin.settings.localConfirm)) {
                return plugin.settings.onDelete.apply($(this).closest('tr'), [plugin]);
              }
              return false;
            });

          $('.tfi-icon', row).removeClass('fa-file-o').addClass(getIcon(file.name));

          if (isImage(file.name)) {
            loadThumb(row, file);
          }

          if ( plugin.settings.enableSelect) {
            row.find('input[type=checkbox]').attr('disabled', 'disabled');
            row.find('input[type=checkbox]').parent().attr('title', plugin.settings.selectTitle);
          } else {
            row.find('input[type=checkbox]').remove();
          }

          row.addClass('tfi-new');
          $('.tfi-filename', row).attr('href', 'javascript:;').removeAttr('target')
            .removeAttr('href').addClass('disabled').text(basename(file.name));
          $('.tfi-btn-view', row).attr('href', 'javascript:;').removeAttr('target')
            .removeAttr('href').addClass('disabled');
          $('.tfi-file-size span', row).text(formatBytes(file.size));

          plugin.settings.table.prepend(row);
        }

        // --------------------------------------------------------------------------------------
        // Clone input field
        // --------------------------------------------------------------------------------------

        let parent = $(this).parent();
        let _input = $(this);
        let clone = _input.clone(true, true);   // No file data
        _input.before(clone.val('').clone(true, true));

        _input.off('change');
        _input.attr('name', _input.attr('data-name')).removeAttr('data-name');
        _input.removeAttr('data-value').removeAttr('data-maxsize');
        _input.removeAttr('id').removeAttr('class')
          .addClass('tfi-clone').removeAttr('value').hide();
        _input.detach();
        formGroup.append(_input);
      },

      // Called when the file checkbox has been clicked
      onCheckboxSelect: function (plugin, file) {
        let id = parseInt(file.id) ?? basepath(file.path);
        let key = `${elementName}_sel`;
        let url = setQueryParameter(document.location.href, key, id);
        if (!$(this).is('.tfi-new')) {
          $.ajax({
            url: url,
            method: 'GET'
          }).done(function (data) {
            $('[type=checkbox]', this).prop('checked', data.file.selected);
          });
        }
      },
      onFileLoad: function (plugin, file) {
      },
      onUrlLoad: function (plugin, file) {
      },
      onDelete: function (plugin, file) {
        // Remove row from table
        let row = $(this);
        if ($(this).is('.tfi-new')) {
          row.remove();
        } else {
          let id = parseInt(file.id) ?? basepath(file.path);
          let key = `${elementName}_del`;
          if ($element.data('uploader') != 'Bs\\Form\\Field\\File') {
            key = 'del';
          }
          let url = setQueryParameter(document.location.href, key, id);

          $.get(url, function (data) {
            row.remove();
            for(let i = 0; i < plugin.settings.value.length; i++) {
              if (plugin.settings.value[i].id == file.id) {
                plugin.settings.value.splice(i, 1);
              }
            }
          });
        }
        return false;
      }
    };

    // --------------------------------------------

    let plugin = this;
    plugin.settings = {};
    let $element = $(element);
    let $inputGroup = $element.closest('.input-group');
    let elementName = '';

    /**
     * constructor
     */
    plugin.init = function () {
      plugin.settings = $.extend({}, defaults, $element.data(), options);

      let table = plugin.settings.table = $(plugin.settings.tableTpl);

      $element.tkFileInput(plugin.settings);
      if (!$element.attr('name').endsWith('[]')) {
        $element.attr('name', $element.attr('name') + '[]');
      }
      $element.attr('data-name', $element.attr('name')).removeAttr('name');
      elementName = $element.data('name').replace('[]', '');

      $element.closest('.input-group').parent().append(table);

      if ($element.prop('readonly')) {
        $element.attr('disabled', 'disabled');
        $('tfi-btn-input input[type=file]', $inputGroup).addClass('disabled');
      }

      // Setup initial field value files
      // TODO: add this to a function so we can use an api call to get the files
      if (plugin.settings.value !== undefined && Array.isArray(plugin.settings.value)) {
        for (let i = 0; i < plugin.settings.value.length; i++) {
          let row = $(plugin.settings.rowTpl);
          let obj = plugin.settings.value[i];
          let fileUrl = '';
          let id = null;
          if (typeof obj === 'object') {
            fileUrl = plugin.settings.dataUrl + obj[plugin.settings.propPath];
            id = obj[plugin.settings.propId];
          } else {
            fileUrl = plugin.settings.dataUrl + obj;
          }

          if (plugin.settings.enableSelect) {
            $('input[type=checkbox]', row).prop('checked', (obj.selected == '1'));
            $('input[type=checkbox]', row).parent().attr('title', plugin.settings.selectTitle);
            $('input[type=checkbox]', row).on('change', function () {
              if (plugin.settings.onCheckboxSelect) {
                return plugin.settings.onCheckboxSelect.apply(row, [plugin, obj]);
              }
            });
          } else {
            $('input[type=checkbox]', row).remove();
          }

          $('.tfi-btn-delete', row).on('click', function (e) {
            $(this).blur();
            if (!plugin.settings.serverConfirm || confirm(plugin.settings.serverConfirm)) {
              return plugin.settings.onDelete.apply(row, [plugin, obj]);
            }
            return false;
          });

          $('.tfi-filename', row).attr('href', fileUrl).text(basename(fileUrl));
          $('.tfi-btn-view', row).attr('href', fileUrl);
          $('.tfi-icon', row).removeClass('fa-file-o').addClass(getIcon(fileUrl));
          if (isImage(fileUrl)) {
            loadThumb(row, fileUrl);
            $('.tfi-filename', row).addClass('ui-lightbox');
            $('.tfi-btn-view', row).addClass('ui-lightbox');
            if ($.fn.magnificPopup) {
              $('.tfi-filename, .tfi-icon', row).magnificPopup({type: 'image'})
            }
          }

          table.append(row);
          plugin.settings.onUrlLoad.apply($('tfi-btn-input input[type=file]', $inputGroup), [plugin, obj]);

          // Check the file exists
          $.ajax({
            type: 'HEAD',
            url: fileUrl,
            data: {
              'nolog':'nolog',
              'crumb_ignore': 'crumb_ignore'
            },
            complete: function (xhr) {
              if (xhr.status !== 200) {
                $('button, a', row).addClass('disabled').on('click', function () {return false;});
                $('.tfi-filename', row).text($('.tfi-filename', row).text() + ' - (Access Error)');
              }
              $('.tfi-file-size span', row).text(formatBytes(xhr.getResponseHeader('Content-Length')));
            }
          });
        }
      }

    };  /// End plugin.init()

    /**
     * @param row
     * @param file
     */
    let loadThumb = function (row, file) {
      if (!plugin.settings.showThumb) return;
      let img = null;
      if (typeof file === 'string') {
        img = $('<a href="#" class="tfi-thumb"><img/></a>');
        $('.tfi-icon', row).replaceWith(img);
        $('img', img).attr('src', file);
        img.attr('href', file);
        img.find('img').css({
          height: plugin.settings.thumbHeight,
          verticalAlign: 'middle',
          border: '1px solid #CCC'
        });
        if ($.fn.magnificPopup) {
          img.magnificPopup({type: 'image'});
        }
      } else {
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function (e) {
          let img = $('<a href="#" class="tfi-thumb"><img/></a>');
          $('.tfi-icon', row).replaceWith(img);
          $('img', img).attr('src', this.result);
          img.attr('href', this.result);
          $('img', img).css({
            height: plugin.settings.thumbHeight,
            verticalAlign: 'top',
            border: '1px solid #CCC'
          });
          if ($.fn.magnificPopup) {
            img.magnificPopup({type: 'image'});
          }
        };
      }

    };

    /**
     * @param uri
     * @param key
     * @param value
     * @returns {string}
     */
    let setQueryParameter = function (uri, key, value) {
      let re = new RegExp("([?&])(" + key + "=)[^&#]*", "g");
      if (uri.match(re))
        return uri.replace(re, '$1$2' + value);

      // need to add parameter to URI
      let paramString = (uri.indexOf('?') < 0 ? "?" : "&") + encodeURIComponent(key) + "=" + encodeURIComponent(value);
      let hashIndex = uri.indexOf('#');
      if (hashIndex < 0)
        return uri + paramString;
      else
        return uri.substring(0, hashIndex) + paramString + uri.substring(hashIndex);
    };

    /**
     * @param path
     * @returns {XML|string}
     */
    let basename = function (path) {
      return path.replace(/\\/g, '/').replace(/.*\//, '');
    };

    /**
     * @param path
     */
    let basepath = function (path) {
      return path.replace(plugin.settings.dataUrl, '');
    };

    /**
     * @param file
     */
    let getExtension = function (filename) {
      let pos = filename.lastIndexOf('.');
      if (pos > -1) {
        return filename.substring(pos + 1);
      }
      return '';
    };

    /**
     * @param filename
     */
    let isImage = function (filename) {
      let ext = getExtension(basename(filename)).toLowerCase();
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
     * @param bytes
     * @param decimals
     * @returns {*}
     */
    let formatBytes = function (bytes, decimals) {
      if (bytes === 0 || isNaN(bytes) || bytes === null) return '0 Byte';
      let k = 1024; // or 1024 for binary
      let dm = decimals + 1 || 2;
      let sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
      let i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    };



    /**
     * @param url
     * @returns {string}
     */
    let getIcon = function (filename) {
      let ext = getExtension(filename);
      switch (ext) {
        case 'zip':
        case 'gz':
        case 'tar':
        case 'gtz':
        case 'rar':
        case '7zip':
        case 'jar':
        case 'pkg':
        case 'deb':
          return 'fa-file-archive-o';
        case 'h':
        case 'c':
        case 'php':
        case 'js':
        case 'css':
        case 'less':
        case 'txt':
        case 'xml':
        case 'xslt':
        case 'json':
          return 'fa-file-code-o';
        case 'ods':
        case 'sdc':
        case 'sxc':
        case 'xls':
        case 'xlsm':
        case 'xlsx':
        case 'csv':
          return 'fa-file-excel-o';
        case 'bmp':
        case 'emf':
        case 'gif':
        case 'ico':
        case 'icon':
        case 'jpeg':
        case 'jpg':
        case 'pcx':
        case 'pic':
        case 'png':
        case 'psd':
        case 'raw':
        case 'tga':
        case 'tif':
        case 'tiff':
        case 'swf':
        case 'drw':
        case 'svg':
        case 'svgz':
        case 'ai':
          return 'fa-file-image-o';
        case 'aiff':
        case 'cda':
        case 'dvf':
        case 'flac':
        case 'm4a':
        case 'm4b':
        case 'midi':
        case 'mp3':
        case 'ogg':
        case 'pcm':
        case 'snd':
        case 'wav':
          return 'fa-file-audio-o';
        case 'avi':
        case 'mov':
        case 'mp4':
        case 'mpg':
        case 'mpeg':
        case 'mkv':
        case 'ogv':
        case 'flv':
        case 'webm':
        case 'wmv':
        case 'asx':
          return 'fa-file-video-o';
        case 'pdf':
          return 'fa-file-pdf-o';
        case 'ppt':
        case 'pot':
        case 'potx':
        case 'pps':
        case 'ppsx':
        case 'pptx':
        case 'pptm':
          return 'fa-file-powerpoint-o';
        case 'doc':
        case 'docm':
        case 'dotm':
        case 'dotx':
        case 'docx':
        case 'dot':
        case 'wri':
        case 'wps':
          return 'fa-file-word-o';
      }
      return 'fa-file-o';
    };

    // call the "constructor" method
    plugin.init();
  };

  // add the plugin to the jQuery.fn object
  $.fn.tkMultiInput = function (options) {
    return this.each(function () {
      if (undefined === $(this).data('tkMultiInput') && undefined === $(this).data('tkInput')) {
        let plugin = new tkMultiInput(this, options);
        $(this).data('tkMultiInput', plugin);
        $(this).data('tkInput', plugin);
      }
    });
  }

})(jQuery);


