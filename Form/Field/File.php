<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 *  A file form field
 *
 * @package Form\Field
 */
class File extends Iface
{
    /**
     * The max size for this file upload in bytes
     * Default: \Tk\Path::string2Bytes(ini_get('upload_max_filesize'))
     * @var int
     */
    protected $maxBytes = 0;

    /**
     * This is the array data for a single file
     * This object should return values from this main array
     *
     * Array (
     *   [name] => filename.txt
     *   [type] => text/text
     *   [tmp_name] => /tmp/phpbviTui
     *   [error] => 0
     *   [size] => 400044
     * )
     *
     * @var array
     */
    private $fileInfo = null;


    /**
     * __construct
     *
     * @param string $name
     * @param null $type
     */
    public function __construct($name, $type = null)
    {
        parent::__construct($name, $type);
        $this->maxBytes = \Tk\Path::string2Bytes(ini_get('upload_max_filesize'));
    }

    /**
     * Returns the fileInfo array or if null it will try to
     * lookup the $_FILES[$fieldName] array for the fileInfo
     * If it exists it will set the instance parameter of FileInfo to this array.
     *
     * @param int $i Use if using multiple files
     * @return array
     */
    public function getFileInfo($i = 0)
    {
        if (!is_array($_FILES[$this->getName()]['name'])) {
            $this->fileInfo = $_FILES[$this->getName()];
        } else {
            $this->fileInfo = array(
                'name' => $_FILES[$this->getName()]['name'][$i],
                'type' => $_FILES[$this->getName()]['type'][$i],
                'tmp_name' => $_FILES[$this->getName()]['tmp_name'][$i],
                'error' => $_FILES[$this->getName()]['error'][$i],
                'size' => $_FILES[$this->getName()]['size'][$i]
            );
        }
        return $this->fileInfo;
    }

    /**
     * Set the fileInfo array.
     *
     * @param array $fileInfo
     * @return $this
     */
    public function setFileInfo($fileInfo)
    {
        $this->fileInfo = $fileInfo;
        return $this;
    }

    /**
     * Returns a more native version of the $_FILES array
     * If only one file is present it will still return an
     *  array of only one element
     *
     * @return array
     */
    public function getFileInfoArray()
    {
        $fileInfoList = array();
        if (!$this->hasFile()) {
            return $fileInfoList;
        }

        if (!is_array($_FILES[$this->getName()]['name'])) {
            $fileInfoList[] = array($this->getFileInfo());
        } else {
            $size = count($_FILES[$this->getName()]['name']);
            for ($i = 0; $i < $size; $i ++) {
                $fileInfoList[] = $this->getFileInfo($i);
            }
        }
        return $fileInfoList;
    }



    /**
     * Get the uploaded filename, will return empty string if no file exists
     * The original name of the file on the client machine.
     *
     * @return string
     */
    public function getFileName()
    {
        $i = $this->getFileInfo();
        if (isset($i['name'])) {
            return $i['name'];
        }
        return '';
    }

    /**
     * Get the uploaded filename, will return empty string if no file exists
     * The original name of the file on the client machine.
     *
     * @return string
     */
    public function getExt()
    {

        $ext = pathinfo($this->getFileName(), PATHINFO_EXTENSION);
        return $ext;
    }

    /**
     * Deprecated
     *
     * @return int
     * @deprecated Use getSize() (v2.0)
     */
    public function getFileSize()
    {
        return $this->getSize();
    }

    /**
     * Get the uploaded file size in bytes
     *
     * @return int
     */
    public function getSize()
    {
        $i = $this->getFileInfo();
        if (isset($i['size'])) {
            return (int)$i['size'];
        }
        return 0;
    }

    /**
     * Get the mime type of the file, if the browser provided this information.
     * An example would be "image/gif". This mime type is however not checked on
     * the PHP side and therefore don't take its value for granted.
     *
     * @return string
     */
    public function getFileType()
    {
        $i = $this->getFileInfo();
        if (isset($i['type'])) {
            return $i['type'];
        }
        return '';
    }


    /**
     * Deprecated
     * @return string
     * @deprecated User getTmpName() (V2.0)
     */
    public function getFileTemp()
    {
        return $this->getTmpName();
    }

    /**
     * Get the file temp location according to PHP
     * @return string
     */
    public function getTmpName()
    {
        $i = $this->getFileInfo();
        if (isset($i['tmp_name'])) {
            return $i['tmp_name'];
        }
        return '';
    }

    /**
     * Deprecated
     *
     * @return int
     * @deprecated Use getError() (V2.0)
     */
    public function getFileErrorId()
    {
        return $this->getError();
    }

    /**
     * Get the file upload error if any
     *
     * @return int
     */
    public function getError()
    {
        $i = $this->getFileInfo();
        return $i['error'];
    }

    /**
     * getErrorString
     *
     * @param int $errorId
     * @return string
     */
    public function getErrorString($errorId = null)
    {
        if ($errorId === null) $errorId = $this->getError();
        switch ($errorId) {
//            case \UPLOAD_ERR_POSTMAX:
//                return "The uploaded file exceeds post max file size of " . ini_get('post_max_size');
            case \UPLOAD_ERR_INI_SIZE :
                return "File exceeds max file size of " . ini_get('upload_max_filesize');
            case \UPLOAD_ERR_FORM_SIZE :
                return "File exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
            case \UPLOAD_ERR_PARTIAL :
                return "File was only partially uploaded.";
            case \UPLOAD_ERR_NO_FILE :
                return "No file was uploaded.";
            case \UPLOAD_ERR_NO_TMP_DIR :
                return "Missing a temporary folder.";
            case \UPLOAD_ERR_CANT_WRITE :
                return "Failed to write file to disk";
            case \UPLOAD_ERR_OK:
            default :
                return "";
        }
    }


    /**
     * Set the parent form object
     *
     * @param \Form\Form $form
     * @return File
     */
    public function setForm(\Form\Form $form)
    {
        $form->setEnctype(\Form\Form::ENCTYPE_MULTIPART);
        return parent::setForm($form);
    }

    /**
     * validate the uploaded file
     *
     * @return bool
     */
    public function validate()
    {
        if ($this->hasFile()) {
            if ($this->getSize() > $this->getMaxFileSize())
                $this->addError('File size to large - Max: ' . \Tk\Path::bytes2String($this->getMaxFileSize()));
            if ($this->getError() != 0)
                $this->addError($this->getErrorString());
        }
    }

    /**
     * Deprecated
     *
     * return boolean
     * @deprecated Use hasFile() (V2.0)
     */
    public function isUploadedFile()
    {
        return $this->hasFile();
    }

    /**
     * Has there been a file submitted?
     *
     * return boolean
     */
    public function hasFile()
    {
        if ($this->getFileName() && $this->getError() !== \UPLOAD_ERR_NO_FILE) {
            return true;
        }
        return false;
    }

    /**
     * Set the max file upload for this field in bytes
     *
     * @param $bytes
     * @return \Form\Field\File
     */
    public function setMaxFileSize($bytes)
    {
        $this->maxBytes = (int)$bytes;
        return $this;
    }

    /**
     * Get the max filesize in bytes for this file field
     *
     * @return int
     */
    public function getMaxFileSize()
    {
        return $this->maxBytes;
    }

    /**
     * Use this to move the uploaded file to the required location
     * NOTE: the path folder will be created using mkdir() if path does not exist
     *
     * @param string $destination
     * @param string $source (Optional)Use for multiple uploads, when manually moving file.
     * @return bool
     */
    public function moveUploadedFile($destination, $source = null)
    {
        if (!is_dir(dirname($destination))) {
            @mkdir(dirname($destination), 0777, true);
        }
        if ($source)
            return move_uploaded_file($source, $destination);
        return move_uploaded_file($this->getTmpName(), $destination);
    }

    /**
     * Render the widget.
     *
     */
    public function show()
    {
        $t = $this->getTemplate();

        $notes = 'Max File Size: ' . \Tk\Path::bytes2String($this->getMaxFileSize(), 0);
        if ($this->notes) {
            $this->notes = $notes . '<br/>' . $this->notes;
        } else {
            $this->notes = $notes;
        }

        $t->appendJsUrl(\Tk\Url::create('/assets/tk-jslib/util.js'));
        // Global filesize check
        $js = <<<JS
config.formMaxFileSize = {$this->getMaxFileSize()};
jQuery(function($) {

  // Problem here, somewhere unbind is getting called and wiping this event.
  // Also this seems to stuff up all the inputs?????????? even for FileMultiple
  // TODO: FIX IT!!!!!!!
  // TODO: use the hook postRender ????
//  $('form').submit(function (e) {
//    var ret = true;
//    var _form = $(this);
//    _form.find('input[type=file]').each( function() {
//      var size = getFileSize(this);
//      if (size > config.formMaxFileSize) {
//        alert('File is to large on field: ' + this.name + ' - ' + $(this).val() + ' ['+bytesToString(size)+']');
//        ret = false;  // stop form submission
//      }
//    });
//    if (!ret) {
//      uOff();
//      return false;
//    }
//    return true;
//  });


  $('input[type=file]').change(function (e) {
      var size = getFileSize(this);
      if (size > config.formMaxFileSize) {
        //alert('WARNING: File is to large on field: ' + this.name + ' - ' + $(this).val() + ' ['+bytesToString(size)+']');
        $(this).parents('.form-group').addClass('error').addClass('has-error');
        $(this).addClass('error');
        $(this).attr('title', 'WARNING: File is to large on field: ' + this.name + ' - ' + $(this).val() + ' ['+bytesToString(size)+']');
        return false;  // stop form submission
      } else {
        $(this).parents('.form-group').removeClass('error').removeClass('has-error');
        $(this).removeClass('error');
      }
  });

});
JS;
        $t->appendJs($js);


        parent::show();


        if (array_key_exists('multiple', $this->getAttrList())) {
            $t->setAttr('element', 'name', $this->name.'[]');
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
<input type="file" var="element" />
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }
}
