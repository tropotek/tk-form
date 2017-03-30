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
     * @var \Tk\UploadedFile[]
     */
    protected $uploadedFiles = array();

    /**
     * @var bool
     */
    protected $delFile= false;

    /**
     * @var string
     */
    protected $previousValue = '';


    /**
     * The path to save the file relative to the dataPath.
     * @var string
     */
    protected $destPath = '';

    /**
     * The full data path to save the file (EG: \Tk\Config::getInstance()->getDataPath()
     * @var string
     */
    protected $dataPath = '';


    /**
     * __construct
     *
     * @param string $name
     * @param string|null $destPath If not set then the file will not be moved and the object will not be set
     * @param string|null $dataPath If not set then the \Tk\Config::getDataPath() will be used
     */
    public function __construct($name, $destPath = null, $dataPath = null)
    {
        parent::__construct($name);
        $this->maxBytes = min( \Tk\File::string2Bytes(ini_get('upload_max_filesize')), \Tk\File::string2Bytes(ini_get('post_max_size')) );
        $this->setType('file');
        if (!$dataPath) {
            $dataPath = \Tk\Config::getInstance()->getDataPath();
        }
        $this->dataPath = $dataPath;
        if ($destPath) {
            $destPath = str_replace($dataPath, '', $destPath);
        }
        $this->destPath = $destPath;
    }

    public function load($values)
    {
        $request = \Tk\Request::create();

        //vd($values, $this->getValue());     // TODO: we will have to load the value initially?????

        // EG Object Path: `/institution/logo/1/logo2.png`

        if ($this->getForm()->isSubmitted()) {
            vd('File::load($values)');
            $this->uploadedFiles = $request->getUploadedFile(str_replace('.', '_', $this->getName()));
            if (!is_array($this->uploadedFiles)) $this->uploadedFiles = array($this->uploadedFiles);
            if (count($this->uploadedFiles) && ($this->uploadedFiles[0] == null || $this->uploadedFiles[0]->getError() == \UPLOAD_ERR_NO_FILE)) {
                $this->uploadedFiles = array();
            }
            if ($this->hasFile() && $this->isValid()) {
                $this->previousValue = $this->getValue();
                $values = array();
                /** @var \Tk\UploadedFile $uploadedFile */
                foreach ($this->getUploadedFiles() as $uploadedFile) {
                    $values[] = $this->destPath . '/' . $uploadedFile->getFilename();
                }
                $this->setValue($values);


                // delete any existing files if new files are valid and path writable

                // move new files if valid

            }
            // Check if the delete file checkbox is checked.



        } else {
            // load object value if not submitted
            parent::load($values);
        }


        return $this;
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

        // TODO add ability to check file types from extension?

        if (!count($this->getUploadedFiles()) && $this->isRequired()) {
            $this->addError(strip_tags('Please select a file to upload'));
        }
        /* @var \Tk\UploadedFile $uploadedFile */
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
     * Set the field value.
     * Set the exact value the field requires to function.
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        if ($this->isArrayField()) {
            $this->value = json_encode($value);
        } else {
            if (is_array($value)) $value = current($value);
            $this->value = $value;
        }
        return $this;
    }

    /**
     * Get the field value(s).
     *
     * @return string|array
     */
    public function getValue()
    {
        if ($this->isArrayField()) {
            return json_decode($this->value);
        }
        return $this->value;
    }

    /**
     * Execute is called after the load methods and only on form submission
     *
     * @param array|\Tk\Request $request
     */
    public function execute($request)
    {
        //vd('File::execute($request)');

        //vd($this->getUploadedFiles());

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
    public function getDeleteEventName()
    {
        return str_replace('.', '_', $this->makeId()) . '-del';
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
     * @param $bytes
     * @return $this
     */
    public function setMaxFileSize($bytes)
    {
        $this->maxBytes = (int)$bytes;
        return $this;
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


    public function deleteFile($fileValue)
    {
        if (!is_array($fileValue)) $fileValue = array($fileValue);

        foreach ($fileValue as $filePath) {

//            if ($this->previousValue && isset($values[$this->getDeleteEventName()])) {
//                $this->delFile = true;
//                if (is_file($this->dataPath . $this->previousValue)) {
//                    @unlink($this->dataPath . $this->previousValue);
//                }
//            }
        }
//                $this->setValue('');



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

                // TODO: Verify that this is working.
                if (!is_dir($targetPath)) {
                    if (!@mkdir($targetPath, 0777, true)) {
                        throw new \Tk\Exception('Internal Permission Error: Cannot move files to destination directory.');
                    }
                }

                $value = array();
                /* @var \Tk\UploadedFile $uploadedFile */
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
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        $this->setNotes('Max. Size: <b>' . \Tk\File::bytes2String($this->getMaxFileSize(), 0) . '</b>' . $this->getNotes());
        $t = parent::getHtml();

        $t->setAttr('element', 'data-maxsize', $this->getMaxFileSize());
        //$t->setAttr('element', 'value', '');

        if ($this->isArrayField()) {
            $t->setAttr('element', 'multiple', 'true');
        }

        if ($this->getValue()) {
            $did = $this->getDeleteEventName();
            $t->setAttr('delete', 'id', $did);
            $t->setAttr('label', 'for', $did);
            $t->setAttr('delete', 'name', $did);
            $t->setAttr('delete', 'value', $did);
            $t->addCss('delWrapper', $did.'-wrap');
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