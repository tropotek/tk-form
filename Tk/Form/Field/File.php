<?php
namespace Tk\Form\Field;

use \Tk\Form;

/**
 * Handle a single file upload field.
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class File extends Input
{

    /**
     * The max size for this file upload in bytes
     * Default: self::string2Bytes(ini_get('upload_max_filesize'))
     * @var int
     */
    protected $maxBytes = 0;

    /**
     * @var \Tk\Request
     */
    protected $request = null;

    /**
     * @var \Tk\UploadedFile
     */
    protected $uploadedFile = null;

    /**
     * @var bool
     */
    protected $delFile= false;

    /**
     * @var string
     */
    protected $previousValue = '';

    /**
     * @var string
     */
    protected $dataPath = '';


    /**
     * __construct
     *
     * @param string $name
     * @param \Tk\Request $request
     * @param string $dataPath
     */
    public function __construct($name, $request, $dataPath = '')
    {
        $this->maxBytes = \Tk\File::string2Bytes(ini_get('upload_max_filesize'));
        $this->request = $request;
        $this->dataPath = $dataPath;
        parent::__construct($name);
        $this->setType('file');

        // Setup file with data ignore empty files
        $this->uploadedFile = $request->getUploadedFile($name);

        $this->setNotes('Max Upload Size: ' . \Tk\File::bytes2String($this->getMaxFileSize(), 0));

    }

    /**
     * Set the field value(s)
     *
     * @param array|string $values
     * @return $this
     */
    public function setValue($values)
    {
        $this->previousValue = $this->getValue();
        parent::setValue($values);
        // set the delete file flag
        if (is_array($values)) {
            $did = $this->getDeleteName();
            if ($this->previousValue && isset($values[$did]) && $values[$did] == $did) {
                $this->delFile = true;
            }
        }
        return $this;
    }

    /**
     * if this field has a "{fieldName}-del" value then the file is marked for delete
     * Use this to know when to delete a file
     *
     * @return boolean
     */
    public function hasDelete()
    {
        return $this->delFile;
    }

    /**
     * @return string
     */
    public function getDeleteName()
    {
        return $this->makeId().'-del';
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
     * get a single uploaded file, if it is an array the first file with data
     * in the list will be returned.
     *
     * @return \Tk\UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * Has there been a file submitted?
     *
     * return boolean
     */
    public function hasFile()
    {
        return ($this->uploadedFile && $this->uploadedFile->getError() == \UPLOAD_ERR_OK);
    }

    /**
     * Use this to move the attached files to the directory in $dir
     *
     * If the directory does not exist it will try to create it for you.
     *
     *
     * @see \Tk\UploadedFile::moveTo
     * @param string $path
     * @return bool|string
     * @internal param string $targetPath
     */
    public function moveTo($path)
    {
        try {
            $path = str_replace($this->dataPath, '', $path);

            if ($this->hasDelete()) {     // Delete previous file if flagged
                if (is_file($this->dataPath . $this->previousValue)) {
                    @unlink($this->dataPath . $this->previousValue);
                }
                $this->setValue('');
            }
            if (!$this->hasFile())
                return false;

            $targetPath = $this->dataPath . $path;
            if (!is_dir(dirname($targetPath))) {
                if (!@mkdir(dirname($targetPath), 0777, true)) {
                    throw new \Tk\Exception('Internal Permission Error: Cannot move files to destination directory.');
                }
            } else {

            }

            $this->uploadedFile->moveTo($targetPath);
            
            if ($this->previousValue != $path && is_file($this->dataPath . $this->previousValue)) {
                @unlink($this->dataPath . $this->previousValue);
            }

            $this->setValue($path);
        } catch (\Exception $e) {
            // TODO: Test this on an error to see the result
            $this->addError($e->getMessage());
            $this->setValue($this->previousValue);
            return false;
        }
        return true;
    }

    /**
     * A basic file validation method.
     *
     * @return bool
     */
    public function isValid()
    {
        if (!$this->hasFile()) {
            return true;
        }
        if ($this->uploadedFile->getError() == \UPLOAD_ERR_NO_FILE && $this->isRequired()) {
            $this->addError('Please select a file to upload');
        }
        if ($this->uploadedFile->getError() != \UPLOAD_ERR_OK) {
            $this->addError($this->uploadedFile->getErrorMessage());
        }
        if ($this->uploadedFile->getSize() > $this->getMaxFileSize()) {
            $this->addError(strip_tags($this->uploadedFile->getFilename()) . ': File to large');
        }
        // Return false if we have errors
        return !empty($this->errors);
    }

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        $this->removeCssClass('form-control');
        $t = parent::getHtml();
        $t->setAttr('element', 'data-maxsize', $this->getMaxFileSize());

        if ($this->getValue()) {
            $did = $this->getDeleteName();
            $t->setAttr('delete', 'id', $did);
            $t->setAttr('label', 'for', $did);
            $t->setAttr('delete', 'name', $did);
            $t->setAttr('delete', 'value', $did);
            $t->addClass('delWrapper', $did.'-wrap');
            $t->setChoice('delete');
        }

        return $t;
    }

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {

        $xhtml = <<<XHTML
<div>
  <input type="text" var="element"/>
  <div choice="delete" var="delWrapper">
    <input type="checkbox" var="delete" id="file-del"/> <label for="file-del" var="label"> Delete File</label>
  </div>
</div>
XHTML;
        return \Dom\Loader::load($xhtml);
    }
}