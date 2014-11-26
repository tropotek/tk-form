<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 * This field is multiple file uploader for a form.
 *
 *
 * Javascripts can be found in the assets/jquery/plugins folder
 *
 * @requires jQuery File Upload Plugin http://blueimp.github.io/jQuery-File-Upload/
 *
 */
class FileMultiple extends File implements \Iterator, \Countable
{
    /**
     * index for Iterator interface
     * @var int
     */
    private $position = 0;

    private $path = '';

    private $maxFiles = 5;


    /**
     *
     * @param type $name
     * @param type $list
     */
    public function __construct($name, $list)
    {
        parent::__construct($name, $list);

        // TODO: clean up old tmp folders of files

    }


    function rewind() {
        $this->position = 0;
    }

    function current() {
        $this->getFileInfo($this->position);
        return $this;
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($_FILES[$this->getName()]['name'][$this->position]);
    }
    public function count($mode = \COUNT_NORMAL)
    {
        return count($_FILES[$this->getName()]['name']);
    }


    public function setPath($path)
    {
        if (!is_dir($this->getConfig()->getDataPath() . $path)) {
            @mkdir($this->getConfig()->getDataPath() . $path, 0777, true);
        }
        $this->path = $path;
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }


    public function setMaxFiles($i)
    {
        $this->maxFiles = $i;
        return $this;
    }

    public function getMaxFiles()
    {
        return $this->maxFiles;
    }


    /**
     * validate the uploaded files
     *
     * @return bool
     */
    public function validate()
    {
        // TODO: validate all files.
    }

    /**
     * Render the widget.
     *
     */
    public function show()
    {
        parent::show();
        $t = $this->getTemplate();
        $t->setAttr('element', 'name', $this->name.'[]');
        $id = $this->getId();



        $css = <<<CSS
.fileinput-button {
  position: relative;
  overflow: hidden;
}
.fileinput-button input {
  position: absolute;
  top: 0;
  right: 0;
  margin: 0;
  opacity: 0;
  -ms-filter: 'alpha(opacity=0)';
  font-size: 200px;
  direction: ltr;
  cursor: pointer;
}
.files li.error {
  color: #B94A48;
}
.files li.up {
  color: #777777;
}
.FileMultiple ul {
  list-style: none;
}

CSS;
        $t->appendCss($css);

        $t->appendJsUrl(\Tk\Url::create($this->getConfig()->getAssetsUrl().'/tk-jslib/Url.js'));

        $t->appendJsUrl(\Tk\Url::create($this->getConfig()->getSelectedThemeUrl().'/js/jquery.mousewheel.min.js'));
        $t->appendJsUrl(\Tk\Url::create($this->getConfig()->getSelectedThemeUrl().'/js/jquery.easing-1.3.pack.js'));

        $t->appendJsUrl(\Tk\Url::create($this->getConfig()->getSelectedThemeUrl().'/js/fancybox2/jquery.fancybox.pack.js'));
        $t->appendCssUrl(\Tk\Url::create($this->getConfig()->getSelectedThemeUrl().'/js/fancybox2/jquery.fancybox.css'));


        $js = <<<JS
;(function ($) {
  $.fn.extend({

    //pass the options variable to the function
    multifile: function (options) {
      //Set the default values, use comma to separate the settings, example:
      var defaults = {
        onChange :  onChange,
        files : [],
        maxFiles : 5
      }
      $.fn.multifile.o = options = $.extend(defaults, options);

      return this.each(function () {
        var o = options;
        // Assign current element to variable, in this case is UL element
        var _this = $(this);
        o._this = _this;
        _this.change(onChange);
        _this.removeAttr('multiple');
        addFiles(o.files);

        _this.parents('.fileinput-button').click(function (e) {
          if ($(this).parent().find('li').length >= o.maxFiles) {
            alert('You have reached the max file upload limit of ' + o.maxFiles + ' files');
            return false;
          }
        });

        var _form = $(_this.parents('form'));
        _form.submit(function (e) { _form.find('.FileMultiple .btn input').remove(); } );
      });

    }
  });

  /**
   *
   * @param Event e
   * @private
   */
  var onChange = function(e) {
    // code goes here
    var _this = $.fn.multifile.o._this;
    var _div = $(_this.parents('div').get(0));

    // Copy file node and reset ready for new file
    var newNode = _this.clone(true);

    var val = _this.val();
    var nval = basename(val).replace('.', '_');

    _this.removeClass('error').removeAttr('title'); // reset node

    // Append input to file list add new input to user selected
    newNode.attr('id', _this.attr('id'));
    _this.removeAttr('id');
    _this.attr('data-file', val);
    _this.remove();
    _this.hide();

    //newNode.removeAttr('id');
    //newNode.attr('data-file', val);
    //newNode.hide();

    // add item to list.
    if (!_div.find('div.files').length) {
      _div.append('<div class="files"><ul></ul></div>');
    }
    $('li.' + nval).remove();

    var li = $('<li class="new ' + nval + '"><a href="javascript:;" class="fa fa-trash-o delete noblock"></a> ' +
        basename(val) + ' [' + bytesToString(getFileSize(_this.get(0))) + ']</li>');
    if (_this.hasClass('error')) {
      li.append(' <b>&lt;-- Error: File to large.</b>');
      li.attr('title', _this.attr('title'));
      li.addClass('error');
    }
    $('.fileinput-button').append(newNode);
    li.append(_this);
    _div.find('ul').prepend(li);
    $.fn.multifile.o._this = newNode;

    $('.delete', li).click(function (e) {
      if (!confirm('Are you sure you want to remove this file?')) return;
      li.remove();
      _this.parents('.form-group').removeClass('error').removeClass('has-error');
    });
  };

  /**
   *
   * @param files
   * @private
   */
  var addFiles = function (files) {
    if (!files.length) return;
    // code goes here
    var _this = $.fn.multifile.o._this;
    var _div = $(_this.parents('div').get(0));

    // add item to list.
    if (!_div.find('div.files').length) {
      _div.append('<div class="files"><ul></ul></div>');
    }

    $.each(files, function (i, file) {
      var view = '';
      if (file.url) {
        view = '<a href="'+file.url+'" class="fancybox fa fa-eye view noblock" target="_blank" title="View File"></a> ';
      }

      var li = $('<li class="up ' + file.name.replace('.', '_') +
        '"><a href="javascript:;" class="fa fa-trash-o delete noblock"></a> ' + view + ' ' + file.name + ' [' + bytesToString(file.size) + ']</li>');
      _div.find('ul').prepend(li);

      $('.delete', li).click(function (e) {
        var url = new Url();
        if (!confirm('Are you sure you want to remove this file?')) return;
        $.post(url.toString(), {'df': file.name},
          function (data) {
            li.remove();
            _this.parents('.form-group').removeClass('error').removeClass('has-error');
          }, 'json'
        ).fail(function () {
          alert('failed to delete file: ' + file.name);
        });

      });
    });


  }

  /**
   *
   * @param mixed obj
   * @private
   */
  var vd = function(obj) {
    if (window.console && window.console.log) {
      window.console.log(obj);
    }
  };

})(jQuery);
JS;
        $t->appendJs($js);




        $fileJs = '';
        if ($this->getPath()) {
            $flist = scandir($this->getConfig()->getDataPath().$this->getPath());
            array_shift($flist);
            array_shift($flist);
            foreach($flist as $k => $v) {
                $fileJs .= '{name:\''.$v.'\', size: '.filesize($this->getConfig()->getDataPath().$this->getPath().'/'.$v).', url: \''.$this->getConfig()->getDataUrl().$this->getPath().'/'.$v.'\'},';
            }
            if ($fileJs) {
                $fileJs = substr($fileJs, 0, -1);
            }
        }

        $js = <<<JS
jQuery(function($) {
  $('#$id').multifile({
    files : [
      $fileJs
    ],
    maxFiles : {$this->getMaxFiles()}
  });
});
JS;
        $t->appendJs($js);


        $js = <<<JS
jQuery(function($) {
    $('.fancybox[href!="zip|doc|docx|pdf|xls|xlsx"]').fancybox({
        maxWidth	: 800,
		maxHeight	: 600,
		fitToView	: false,
		width		: '70%',
		height		: '70%',
		autoSize	: false,
		openEffect	: 'none',
		closeEffect	: 'none',
		type : 'iframe',
		afterClose : function (e) {
		  // Why is this needed HACK!!!
		  $('.fancybox-overlay').remove();
		}
      });
});
JS;
            $t->appendJs($js);

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
  <span class="btn btn-default fileinput-button">
    <i class="glyphicon glyphicon-plus"></i>
    <span>Select files...</span>
    <!-- The file input field used as target for the file upload widget -->
    <input type="file" var="element" />
  </span>
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }


}