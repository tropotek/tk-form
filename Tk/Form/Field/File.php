<?php
namespace Tk\Form\Field;

use \Tk\Form;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class File extends Input
{

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
    private $fileInfo = array();

    /**
     * The max size for this file upload in bytes
     * Default: self::string2Bytes(ini_get('upload_max_filesize'))
     * @var int
     */
    protected $maxBytes = 0;


    /**
     * __construct
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->maxBytes = self::string2Bytes(ini_get('upload_max_filesize'));
        parent::__construct($name);
        $this->setType('file');
    }

    /**
     * Set the field value(s)
     *
     * @param array|string $values
     * @return $this
     */
    public function setValue($values)
    {
        return $this;
    }

    /**
     * Get the field value(s).
     * 
     * @return string|array
     */
    public function getValue()
    {
        return '';
    }
    
    
    /**
     * Set the form for this element
     *
     * @param Form $form
     * @return $this
     */
    public function setForm(Form $form)
    {
        parent::setForm($form);
        $form->setAttr('enctype', Form::ENCTYPE_MULTIPART);
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
     * Returns the fileInfo array or if null it will try to
     * lookup the $_FILES[$fieldName] array for the fileInfo
     * If it exists it will set the instance parameter of FileInfo to this array.
     *
     * @return array
     */
    public function getFileInfo()
    {
        if (!count($this->fileInfo) && $this->hasFile()) {
            if (!is_array($_FILES[$this->getName()]['name'])) {
                $this->fileInfo = $_FILES[$this->getName()];
            } else {
                $this->fileInfo = array();
                foreach ($_FILES[$this->getName()]['name'] as $i => $name) {
                    $this->fileInfo[] = array(
                        'name' => $_FILES[$this->getName()]['name'][$i],
                        'type' => $_FILES[$this->getName()]['type'][$i],
                        'tmp_name' => $_FILES[$this->getName()]['tmp_name'][$i],
                        'error' => $_FILES[$this->getName()]['error'][$i],
                        'size' => $_FILES[$this->getName()]['size'][$i]
                    );
                }
            }
            if ( ($this->isArray() && isset($this->fileInfo['name'])) ) {
                $this->fileInfo = array($this->fileInfo);
            }
        }
        return $this->fileInfo;
    }

    /**
     * Has there been a file submitted?
     *
     * return boolean
     */
    public function hasFile()
    {
        if ($this->isArray()) {
            if (isset($_FILES[$this->getName()]['name'][0]) && $_FILES[$this->getName()]['name'][0] != '') {
                return true;
            }
        } else {
            if (!empty($_FILES[$this->getName()])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Has there been a file submitted?
     *
     * return boolean
     */
    public function count()
    {
        if ($this->hasFile()) {
            if ($this->isArray()) {
                return count($_FILES[$this->getName()]['name']);
            } else {
                return 1;
            }
        }
        return 0;
    }

    /**
     * validate the uploaded file
     *
     * @param bool $required
     * @return bool
     */
    public function isValid($required = false)
    {
        if (!$this->hasFile()) {
            return;
        }
        $infoArray = $this->getFileInfo();
        
        foreach($infoArray as $info) {
            if ($info['size'] > $this->getMaxFileSize()) {
                $this->addError($info['name'] . ': File to large');
            }
            if ($info['error'] != \UPLOAD_ERR_OK && ($required || $info['error'] != \UPLOAD_ERR_NO_FILE)) {
                $name = '';
                if (!empty($info['name'])) {
                    $name = $info['name'] . ': ';
                }
                $this->addError($name . self::getErrorString($info['error']));
            }
        }
    }

    /**
     * Use this to move the attached files to the directory in $dir
     *
     * If the directory does not exist it will try to create it for you.
     *
     * You may still use the move_uploaded_file() function standalone if you prefer
     * to have control of the saved filenames
     *
     * @param string $dir
     * @return bool
     */
    public function moveUploadedFile($dir)
    {
        $b = false;
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $info = $this->getFileInfo();
        if (!$this->isArray()) {
            $info = array($info);
        }
        foreach($this->getFileInfo() as $i => $info) {
            $b = (move_uploaded_file($info['tmp_name'], $dir . '/' . $info['name']) || $b);
        }
        return $b;
    }

    /**
     * Get the uploaded filename, will return empty string if no file exists
     * The original name of the file on the client machine.
     *
     * @param $filename
     * @return string
     */
    static public function getExt($filename)
    {
        $ext = pathinfo(basename($filename), PATHINFO_EXTENSION);
        return $ext;
    }

    /**
     * getErrorString
     *
     * @param int $errorId
     * @return string
     */
    static public function getErrorString($errorId = null)
    {
        switch ($errorId) {
            case \UPLOAD_ERR_POSTMAX:
                return "The uploaded file exceeds post max file size of " . ini_get('post_max_size');
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
     * Get the bytes from a string like 40M, 10T, 100K
     *
     * @param string $str
     * @return int
     */
    static function string2Bytes($str)
    {
        $sUnit = substr($str, -1);
        $iSize = (int)substr($str, 0, -1);
        switch (strtoupper($sUnit)) {
            case 'Y' :
                $iSize *= 1024; // Yotta
            case 'Z' :
                $iSize *= 1024; // Zetta
            case 'E' :
                $iSize *= 1024; // Exa
            case 'P' :
                $iSize *= 1024; // Peta
            case 'T' :
                $iSize *= 1024; // Tera
            case 'G' :
                $iSize *= 1024; // Giga
            case 'M' :
                $iSize *= 1024; // Mega
            case 'K' :
                $iSize *= 1024; // kilo
        }
        return $iSize;
    }

    /**
     * Convert a value from bytes to a human readable value
     *
     * @param int $bytes
     * @return string
     * @author http://php-pdb.sourceforge.net/samples/viewSource.php?file=twister.php
     */
    static function bytes2String($bytes, $round = 2)
    {
        $tags = array('b', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $index = 0;
        while ($bytes > 999 && isset($tags[$index + 1])) {
            $bytes /= 1024;
            $index++;
        }
        $rounder = 1;
        if ($bytes < 10) {
            $rounder *= 10;
        }
        if ($bytes < 100) {
            $rounder *= 10;
        }
        $bytes *= $rounder;
        settype($bytes, 'integer');
        $bytes /= $rounder;
        if ($round > 0) {
            $bytes = round($bytes, $round);
            return  sprintf('%.'.$round.'f %s', $bytes, $tags[$index]);
        } else {
            return  sprintf('%s %s', $bytes, $tags[$index]);
        }
    }


}