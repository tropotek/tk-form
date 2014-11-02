<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 * A file form field
 * This fied assumes that there is a \Tk\Model\Object in the form object.
 *
 * @package Form\Field
 * @todo Finish and test this object....
 */
class FileModel extends File
{
    

    protected $viewEvent = null;
    protected $deleteEvent = null;
    protected $processEvent = null;

    /**
     * __construct
     *
     * @param string $name
     * @param \Form\Event\File $event This event process the file Default is: Form_Event_File
     */
    public function __construct($name, $event = null)
    {
        parent::__construct($name, new \Form\Type\File());
        
        
        $this->processEvent = $event;
        if (!$this->processEvent) {
           $this->processEvent = new \Form\Event\File();
        }
        $this->processEvent->setField($this);
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
        // Add default file upload event
        $form->attach($this->processEvent);

        // Add button events to field
        if ($this->enableEvents) {
            $this->viewEvent = new ViewFile('view' . ucfirst($this->getName()));
            $this->viewEvent->setHidden()->setLabel('View');
            $this->deleteEvent = new DeleteFile('delete' . ucfirst($this->getName()));
            $this->deleteEvent->setHidden()->setLabel('Delete');
            $form->attach($this->viewEvent, 'view');
            $form->attach($this->deleteEvent, 'delete');
        }
        return parent::setForm($form);
    }

    /**
     *
     * @param bool $b
     * @return $this
     */
    public function enableEvents($b)
    {
        $this->enableEvents = $b;
        return $this;
    }

    /**
     * When using the Form object do not use move_uploaded_file unlesss you know what it affects
     * when using the file field.
     *
     * Use this method to move a file from the Form environment default location to the
     * new destination. Check your form result object or array for the current htdoc data path.
     * This path must be prepended with the config system.dataPath variable.
     *
     * The source and destination parameters should be full paths to the file locations
     *
     * NOTICE: Files in the default form folder will be deleted after 24 hours if not moved to a perminent location
     *
     * @param string $source Usually created from an object param. EG: $source = Tk_Config::get('system.dataPath').$obj->getImage();
     * @param string $destination
     * @return bool
     */
//    static function moveUploadedFile($source, $destination)
//    {
//
//        if (!is_file($source)) {
//            \Tk\Log\Log::write('Source file does not exist: ' . $source, Tk\Log::ERROR);
//            return false;
//        }
//
//        // check dest dir, create as required.
//        if (!is_dir(dirname($destination))) {
//            if (!mkdir(dirname($destination), 0777, true)) {
//                \Tk\Log\Log::write('Cannot Create Directory: ' . dirname($destination), \Tk\Log\Log::ERROR);
//                return false;
//            }
//        }
//
//        // move file
//        if (!rename($source, $destination)) {
//            \Tk\Log\Log::write('Cannot move file: ' . $source . ' - ' . $destination, \Tk\Log\Log::ERROR);
//            return false;
//        }
//
//        // Check for old temp files and delete them, only in the form ID folder
//        foreach (new \RecursiveDirectoryIterator(dirname(dirname($source))) as $fileInfo) {
//            if($fileInfo->isDir()) continue;
//            if ($fileInfo->getMTime() < time()-(60*60*1)) {
//                @unlink($fileInfo->getPath());
//            }
//        }
//
//        // Check if the field has no more files, then delete the dir
//        if (is_dir(dirname($source))) {
//            $arr = scandir(dirname($source));
//            array_shift($arr);
//            array_shift($arr);
//            if (!count($arr)) {
//                @rmdir(dirname($source));
//            }
//        }
//
//        // Check if the form has no more files, then delete the dir
//        if (is_dir(dirname(dirname($source)))) {
//            $arr = scandir(dirname(dirname($source)));
//            array_shift($arr);
//            array_shift($arr);
//            if (!count($arr)) {
//                @rmdir(dirname(dirname($source)));
//            }
//        }
//        return true;
//    }




    /**
     * Render the widget.
     *
     */
    public function show()
    {
        $t = $this->getTemplate();
        // This needs to be here, see docs about post_max_size errors....
        // Best thing to do is have the post size double that of the max file size...
        if (self::hasMaxPostError()) {
            $postMax = ini_get('post_max_size'); //grab the size limits...
            $msg = "Please note files larger than {$postMax} will result in this error!
                Please be advised this is not a limitation in the site, This is a limitation of the hosting server.
                If you have access to the php ini file you can fix this by changing the post_max_size setting.";
            $this->getForm()->addFieldError($this->getFileName(), $msg);
        }

        $notes = 'Max File Size: ' . \Tk\Path::bytes2String($this->getMaxFileSize(), 1);
        $fieldValues = $this->type->getFieldValues();
        if ( isset($fieldValues[$this->getName()]) && $fieldValues[$this->getName()] ) {
            $notes .= ' <span class="path">(`' . $fieldValues[$this->getName()] . '`)</span>';
        }
        if ($this->notes) {
            $this->notes = $notes . '<br/>' . $this->notes;
        } else {
            $this->notes = $notes;
        }

        parent::show();

        // No need to render readonly on file field anyway as not relevent
        $t->setAttr('element', 'readonly', null);   // Remove readonly attribute for IE browsers

        if ($this->enableEvents && isset($fieldValues[$this->getName()]) && $fieldValues[$this->getName()]) {
            $this->deleteEvent->show();
            $t->appendTemplate('events', $this->deleteEvent->getTemplate());
            $this->viewEvent->setRedirectUrl(\Tk\Url::createDataUrl( $fieldValues[$this->getName()] ));
            $this->viewEvent->show();
            $t->appendTemplate('events', $this->viewEvent->getTemplate());
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
<div>
  <input type="file" var="element" />
  <div class="fieldButtons" var="events"></div>
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }
}

/**
 *
 *
 * @package Form
 */
class DeleteFile extends \Form\Event\Button
{
    /**
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }

    /**
     * execute
     *
     * @param Form $form
     */
    public function update($form)
    {
        $object = $form->getObject();
        $name = lcFirst(str_replace('delete', '', $this->getName()));

        if ($object instanceof \Tk\Db\Model) {
            if (isset($object->$name) && $object->$name != null) {
                @unlink(\Tk\Config::getInstance()->getDataPath() . $object->$name);
                $object->$name = '';
                $object->update();
            }
        } else if (is_array($object)) {
            if (!empty($object[$name])) {
                @unlink(\Tk\Config::getInstance()->getDataPath() . $object[$name]);
                $object[$name] = '';
            }
        }

        $url = \Tk\Request::getUri()->delete($this->getName());
        $form->setRedirectUrl($url);
    }


    /**
     * Render the widget.
     *
     */
    public function show()
    {
        $t = $this->getTemplate();
        parent::show();
    }
}

/**
 *
 *
 * @package Form
 */
class ViewFile extends \Form\Event\Link
{

    /**
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }

    /**
     * show
     */
    public function show()
    {
        $this->addCssClass('view');
        $this->addCssClass('lightbox');
        parent::show();
        $t = $this->getTemplate();

        // Setup fancybox lightbox for images if available.
        if (class_exists('\Js\Fancybox')) {
            \Js\Fancybox::create($t)->show();
        }
    }
}


