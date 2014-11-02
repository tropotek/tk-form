<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Type;

/**
 * Use this type for File fields
 *
 * @package Form
 * @deprecated TODO: need to rework this as I think it is no longer required.
 */
class File extends Iface
{
    /**
     * @var bool
     */
    protected $moved = false;
    

    /**
     * Convert the basic form submitted string field value
     * into its correct complex type.
     *
     * @param array|stdClass $array
     * @return mixed
     */
    public function toObject($array)
    {
        if ($this->moved) { // Ensure it only happens once per page load
            return;
        }
        $config = \Tk\Config::getInstance();
        $name = $this->field->getName();
        
        if (!isset($array[\Form\Form::HIDDEN_SUBMIT_ID]) || !$this->field->isUploadedFile()) {
            if (!empty($array[$name])) {
                $this->fieldValues[$name] = $array[$name];
            }
            return;
        }
        
        if ($this->field->getFileError()) {
            $this->field->getForm()->addFieldError($name, \Form\Field\File::getErrorString($this->field->getFileError()));
            return;
        }

        $htdst = '/Form/' . $array[\Form\Form::HIDDEN_SUBMIT_ID] . '-' . $this->getSession()->getName() . '/' . $name . '/' . $_FILES[$name]['name'];

        $this->fieldValues[$name] = $htdst;

        if (!is_dir(dirname($config->getDataPath() . $htdst))) {
            mkdir(dirname($config->getDataPath() . $htdst), 0777, true);
        }

        if (!move_uploaded_file($this->field->getFileTemp(), $config->getDataPath() . $htdst)) {
            $this->field->addError($name, \Form\Field\File::getErrorString($this->field->getFileError()));
            \Tk\Log\Log::write('Error: Moving file for field ' . $this->field->getName(), \Tk\Log\Log::ERROR);
        }
        
        $this->moved = true;
    }


}