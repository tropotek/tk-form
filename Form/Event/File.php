<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Event;


/**
 * A default image handling event
 * This can be used in situations where you want the files for an object saved
 *  to '{data_dir}/{className}/{fieldName}_{id}.{ext}
 *
 * @package Form\Event
 */
class File extends Hidden
{

    /**
     * @var \Form\Field\File
     */
    protected $field = null;


    /**
     *
     * @param \Form\Field\File $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * executed
     *
     * @param \Form\Form $form
     */
    public function update($form)
    {
        if ($this->field->isUploadedFile() && !$form->hasErrors()) {
            $this->processFile($this->field);
        }
    }

    /**
     * processFile
     *
     * @param \Form\Field $field
     */
    public function processFile($field)
    {
        $object = $this->form->getObject();
        $htorg = '';
        $htdst = '';

        $httmp = $field->getSubFieldValue($field->getName());
        if (is_object($object)) {
            $name = $field->getName();
            $htorg = $object->$name;
            $htdst = $this->makeObjectPath($object, $field);
            $object->$name = $htdst;
        } else {
            $htorg = $object[$field->getName()];
            $htdst = $this->makeArrayPath($object, $field);
            $object[$field->getName()] = $htdst;
        }
        
        $dataPath = \Tk\Config::getInstance()->getDataPath();
        if (!\Form\Field\File::moveUploadedFile($dataPath . $httmp, $dataPath . $htdst)) {
            $this->field->addError('Error Moving Temp file `' . $httmp . '`');
            @unlink($dataPath . $httmp);
            return;
        }
        
        $field->setSubFieldValue($field->getName(), $htdst);
        $field->setRawValue($htdst);

        // delete any old file
        if ($htorg != $htdst && @is_file($dataPath . $htorg)) {
            @unlink($dataPath . $htorg);
        }
        // Save object here in case of upload errors
        if ($object instanceof \Tk\Db\Model) {
            $object->save();
        }
    }

    /**
     * Make a path for object files
     *
     * @param \Tk\Db\Model $object
     * @param \Form\Field\File $field
     * @return string
     */
    public function makeObjectPath($object, $field)
    {
        $get = 'get' . ucfirst($field->getName());
        $htorg = $object->$get();
        $ext = \Tk\Path::getFileExtension($htorg);
        $arr = explode('_', get_class($object));
        $class = array_pop($arr);
        $htdst = '/' . ucfirst($class) . '/' . $object->getVolitileId() . '/' . $field->getName() . '.' . $ext;
        return $htdst;
    }

    /**
     * Make a path for array objects
     *
     * @param array $array
     * @param \Form\Field\File $field
     * @return string
     */
    public function makeArrayPath($array, $field)
    {
        $htdst = '/Array/' . $field->getName() . '/' . $field->getFileName();
        return $htdst;
    }
}