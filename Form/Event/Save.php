<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Event;

/**
 * This event is mainly used by DB objects to save their contents to the DB
 *
 * @package Form\Event
 */
class Save extends Button
{

    /**
     * __construct
     *
     * @param string $name
     * @param string $icon
     */
    public function __construct($name, $icon = 'fa fa-check')
    {
        parent::__construct($name, $icon);
    }

    /**
     * execute
     *
     * @param Form $form
     */
    public function update($form)
    {
        $object = $form->getObject();

        if ($object instanceof \Tk\Db\Model) {
            $form->loadObject($object);
            $form->addFieldErrors($object->getValidator()->getErrors());
        }

        if ($form->hasErrors()) {
            if (!\Mod\Notice::hasMessages()) {
                \Mod\Notice::addError('The form contains errors.');
            }
            return;
        }
        if ($object instanceof \Tk\Db\Model) {
            $object->save();
            \Mod\Notice::addSuccess('Form submitted successfully');
        }

    }

}
