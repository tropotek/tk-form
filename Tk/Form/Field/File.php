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
     * @var \Tk\UploadedFile[]
     */
    protected $uploadedFiles = null;

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
        $this->maxBytes = min( \Tk\File::string2Bytes(ini_get('upload_max_filesize')), \Tk\File::string2Bytes(ini_get('post_max_size')) );
        $this->request = $request;
        $this->dataPath = $dataPath;
        parent::__construct($name);
        $this->setType('file');

        $this->setNotes('Max. Size: <b>' . \Tk\File::bytes2String($this->getMaxFileSize(), 0) . '</b>');

        // Setup file with data ignore empty files
        $this->uploadedFiles = $request->getUploadedFile(str_replace('.', '_', $this->getName()));

        if (!is_array($this->uploadedFiles)) $this->uploadedFiles = array($this->uploadedFiles);
        if (count($this->uploadedFiles) && ($this->uploadedFiles[0] == null || $this->uploadedFiles[0]->getError() == \UPLOAD_ERR_NO_FILE)) {
            $this->uploadedFiles = array();
        }

    }

    public function load($values)
    {
        $this->previousValue = $this->getValue();
        parent::load($values);

        // TODO: Not sure this stuff belongs here??
        if (is_array($values) && $this->getForm()->isSubmitted()) {
            $did = str_replace('.', '_', $this->getDeleteName());
            //if ($this->previousValue && isset($values[$did]) && $values[$did] == $did) {
            if ($this->previousValue && isset($values[$did])) {
                $this->delFile = true;
                if (is_file($this->dataPath . $this->previousValue)) {
                    @unlink($this->dataPath . $this->previousValue);
                }
                $this->setValue('');
            }
        }
        return $this;
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
     * Get the max filesize in bytes for this file field
     *
     * @return int
     */
    public function getMaxFileSize()
    {
        return $this->maxBytes;
    }

    /**
     * Get a single uploaded file, default is to return the first file in the list.
     *
     * @param int $i For multiple files
     * @return null|\Tk\UploadedFile
     */
    public function getUploadedFile($i = 0)
    {
        if (isset($this->uploadedFiles[$i]))
            return $this->uploadedFiles[$i];
    }

    /**
     * get a single uploaded file, if it is an array the first file with data
     * in the list will be returned.
     *
     * @return \Tk\UploadedFile[]
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * Has there been a file submitted?
     *
     * return boolean
     */
    public function hasFile()
    {
        return (count($this->getUploadedFiles()) > 0);
    }

    /**
     * Use this to move the attached files to the directory in $dir
     *
     * If this is a single file then the $filepath is the full path (including filename)
     * of the destination location.
     *
     * If this is a multiple file field (isFieldArray() == true) then the $filepath
     * is the directory of the destination location.
     *
     * If you need more control get the updaloadedFiles() and do it manually
     *
     * If the directory does not exist it will try to create it for you.
     *
     *
     * @see \Tk\UploadedFile::moveTo
     * @param string $filepath The full destination path with filename
     * @internal param string $targetPath
     */
    public function moveTo($filepath)
    {
        try {
            if (!$this->hasFile()) return;
            $filepath = str_replace($this->dataPath, '', $filepath);
            $targetPath = $this->dataPath . $filepath;

            $value = '';
            if (!$this->isArrayField()) {   // single file
                if (!is_dir(dirname($targetPath))) {
                    if (!@mkdir(dirname($targetPath), 0777, true)) {
                        throw new \Tk\Exception('Internal Permission Error: Cannot move files to destination directory.');
                    }
                }
                $this->getUploadedFile()->moveTo($targetPath);
                // TODO: Still not sure this belongs here???
                if ($this->previousValue != $filepath && is_file($this->dataPath . $this->previousValue)) {
                    @unlink($this->dataPath . $this->previousValue);
                }
                $value = $filepath;
            } else {     // multiple files
                if (!is_dir($targetPath)) {
                    if (!@mkdir($targetPath, 0777, true)) {
                        throw new \Tk\Exception('Internal Permission Error: Cannot move files to destination directory.');
                    }
                }

                $value = array();
                /** @var \Tk\UploadedFile $uploadedFile */
                foreach ($this->getUploadedFiles() as $uploadedFile) {
                    $filepath =  basename(strip_tags($targetPath.'/'.$uploadedFile->getFilename()));
                    $uploadedFile->moveTo($filepath);
                    $value[] = $filepath;
                }

            }

            $this->setValue($value);
        } catch (\Exception $e) {
            // TODO: Test this on an error to see the result
            $this->addError($e->getMessage());
            $this->setValue($this->previousValue);
        }
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
        // TODO: add ability to check file types from extension?


        if (!count($this->getUploadedFiles()) && $this->isRequired()) {
            $this->addError(strip_tags('Please select a file to upload'));
        }
        /** @var \Tk\UploadedFile $uploadedFile */
        foreach ($this->getUploadedFiles() as $uploadedFile) {
            if ($uploadedFile->getError() != \UPLOAD_ERR_OK) {
                $this->addError(strip_tags($uploadedFile->getFilename()) .': '. $uploadedFile->getErrorMessage());
            }
            if ($uploadedFile->getSize() > $this->getMaxFileSize()) {
                $this->addError(strip_tags($uploadedFile->getFilename()) . ': File to large');
            }
        }
        // Return false if we have errors
        return !count($this->errors);
    }

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        $t = parent::getHtml();
        $t->setAttr('element', 'data-maxsize', $this->getMaxFileSize());

        if ($this->isArrayField()) {
            $t->setAttr('element', 'multiple', 'true');
        }

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
        $xhtml = <<<HTML
<div>
  <input type="text" class="form-control fileinput" var="element"/>
  <div choice="delete" var="delWrapper">
    <input type="checkbox" class="" var="delete" id="file-del"/> <label for="file-del" var="label"> Delete File</label>
  </div>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }
}