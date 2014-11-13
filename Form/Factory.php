<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form;

/**
 * The Form Factory is a place to crate all
 * elements to use a form.
 *
 * Its primary purpose is to give extendability using observers to override
 * elements upon creation.
 *
 *
 * @package Form
 */
class Factory extends \Tk\Object
{
    /**
     * @var Factory
     */
    static $instance = null;

    /**
     * @var mixed
     */
    protected $object = null;





    /**
     * Get an instance of this object
     *
     * @return Factory
     */
    static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * This function allows observers to access the current
     * created object for modification/replacement if required.
     *
     * @return mixed
     */
    public function getCurrentObject()
    {
        return $this->object;
    }


    //-------------- FACTORY METHODS ------------------
    // Try to list them in alphabetical order please....




    /**
     * Create a form
     *
     * @param string $formId
     * @param mixed $object
     * @return Form
     */
    public function createForm($formId, $object = null)
    {
        $this->object = new Form($formId, $object);
        $this->notify('createForm');
        $this->object->addCssClass('form-horizontal');
        return $this->object;
    }

    /**
     * Create a form
     *
     * @param string $formId
     * @param mixed $object
     * @param \Tk\Url $backUrl
     * @return Form
     * @deprecated Use createForm() then call createDefaultEvents...
     */
    public function createDefaultForm($formId, $object = null, $backUrl = null)
    {
        $form = new Form($formId, $object);
        if (!$backUrl) {
            $backUrl = $this->getUri();
        }
        $form->attach($this->createEventSave('update'), 'update')->setRedirectUrl($backUrl);
        $form->attach($this->createEventSave('save'), 'save')->setRedirectUrl($this->getUri());
        $form->attach($this->createEventCancel('cancel', $backUrl));
        //$form->attach($this->createEventLink('cancel'), 'cancel')->setRedirectUrl($backUrl);
        $this->object = $form;
        $this->notify('createDefaultForm');
        return $form;
    }

    /**
     * Attach default form events.
     *
     * @param \Form\Form $form
     * @param \Tk\Url $backUrl
     * @return \Form\Form
     */
    public function createDefaultEvents($form, $backUrl = null, $createUrl = null)
    {
        if (!$backUrl) {
            $backUrl = $this->getUri();
        }
        if (!$createUrl) {
            $createUrl = $this->getUri();
        }

        if ($form->getObject()->id) {
            $form->attach($this->createEventSave('update', 'fa fa-arrow-left'))->setRedirectUrl($backUrl);
            $form->attach($this->createEventSave('save', 'fa fa-refresh'))->setRedirectUrl($this->getUri());
        } else {
            $form->attach($this->createEventSave('create', 'fa fa-refresh'))->setRedirectUrl($createUrl);
        }


        //$form->attach($this->createEventLink('cancel'))->setRedirectUrl($backUrl);
        $form->attach($this->createEventCancel('cancel', $backUrl));
        $this->object = $form;
        $this->notify('createDefaultEvents');
        return $form;
    }

    /**
     * Create Form Renderer
     *
     * @param Form $form
     * @return Renderer
     */
    public function createFormRenderer($form)
    {
        $this->object = new Renderer($form);
        $this->notify('createFormRenderer');
        return $this->object;
    }

    /**
     * Create A Static Form Renderer
     *
     * @param Form $form
     * @param \Dom\Template $template
     * @return Renderer
     */
    public function createStaticFormRenderer($form, $template)
    {
        $this->object = new StaticRenderer($form, $template);
        $this->notify('createStaticFormRenderer');
        return $this->object;
    }




    // ----------- FORM EVENTS ---------------


    /**
     * Create
     *
     * @param string $name
     * @return Event\File
     */
    public function createEventFile($name = '')
    {
        $this->object = new Event\File($name);
        $this->notify('createEventFile');
        return $this->object;
    }

    /**
     * Create
     *
     * @param string $name
     * @return Event\Link
     */
    public function createEventLink($name)
    {
        $this->object = new Event\Link($name);
        $this->notify('createEventLink');
        return $this->object;
    }


    /**
     * Create
     *
     * @param string $name
     * @param string $icon
     * @return Event\Save
     */
    public function createEventSave($name, $icon = 'fa fa-check')
    {
        $this->object = new Event\Save($name, $icon);
        $this->notify('createEventSave');
        return $this->object;
    }


    /**
     * Create
     *
     * @param string $name
     * @param \Tk\Url|string $url
     * @return Event\Cancel
     */
    public function createEventCancel($name, $url)
    {
        $this->object = new Event\Cancel($name);
        $this->object->setRedirectUrl($url);
        $this->notify('createEventCancel');
        return $this->object;
    }








    // ----------- FORM FIELDS ---------------


    /**
     * Create
     *
     * @param string $name
     * @param string|\Tk\Url $ajaxUrl
     * @param int $minLength
     * @return Field\Autocombo
     */
    public function createFieldAutocombo($name, $ajaxUrl, $minLength = 1)
    {
        $this->object = new Field\Autocombo($name, $ajaxUrl, $minLength);
        $this->notify('createFieldAutocombo');
        return $this->object;
    }

    /**
     * Create
     *
     * @param string $name
     * @param string|\Tk\Url $ajaxUrl
     * @param int $minLength
     * @return Field\Autocombo
     */
    public function createFieldAutocomplete($name, $ajaxUrl, $minLength = 1)
    {
        $this->object = new Field\Autocomplete($name, $ajaxUrl, $minLength);
        $this->notify('createFieldAutocomplete');
        return $this->object;
    }

    /**
     * Create
     *
     * @param string $name
     * @return Field\Captcha
     */
    public function createFieldCaptcha($name)
    {
        $ad = new Field\Captcha\Basic();
        $this->object = new Field\Captcha($name, $ad);
        $this->notify('createFieldCaptcha');
        return $this->object;
    }

    /**
     * Create
     *
     * @param string $name
     * @return Field\Checkbox
     */
    public function createFieldCheckbox($name)
    {
        $this->object = new Field\Checkbox($name);
        $this->notify('createFieldCheckbox');
        return $this->object;
    }

    /**
     * Create
     *
     * @param string $name
     * @param array $options
     * @return Field\CheckboxGroup
     */
    public function createFieldCheckboxGroup($name, $options = null)
    {
        $this->object = new Field\CheckboxGroup($name, $options);
        $this->notify('createFieldCheckboxGroup');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @param array $options
     * @return Field\CheckboxSelect
     */
    public function createFieldCheckboxSelect($name, $options = null)
    {
        $this->object = new Field\CheckboxSelect($name, $options);
        $this->notify('createFieldCheckboxSelect');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @param array $options
     * @return Field\CheckboxSelectBit
     */
    public function createFieldCheckboxSelectBinary($name, $options = null)
    {
        $this->object = new Field\CheckboxSelectBinary($name, $options);
        $this->notify('createFieldCheckboxSelectBinary');
        return $this->object;
    }

    /**
     * Create
     *
     * @param string $name
     * @return Field\CodeArea
     */
    public function createFieldCodeArea($name)
    {
        $this->object = new Field\CodeArea($name);
        $this->notify('createFieldCodeArea');
        return $this->object;
    }

    /**
     * Create
     *
     * @param string $name
     * @param bool $isNull
     * @return Field\Date
     */
    public function createFieldDate($name, $isNull = true)
    {
        $this->object = new Field\Date($name, $isNull);
        $this->notify('createFieldDate');
        return $this->object;
    }

    /**
     * Create
     *
     * @param string $name
     * @param bool $isNull
     * @return Field\DateSet
     */
    public function createFieldDateSet($name, $isNull = true)
    {
        $this->object = new Field\DateSet($name, $isNull);
        $this->notify('createFieldDateSet');
        return $this->object;
    }

    /**
     * Create
     *
     * @param string $name
     * @param bool $isNull
     * @return Field\DateTime
     */
    public function createFieldDateTime($name, $isNull = true)
    {
        $this->object = new Field\DateTime($name, $isNull);
        $this->notify('createFieldDateTime');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @param array $options
     * @param Type\Iface $type
     * @return Field\DualSelect
     */
    public function createFieldDualSelect($name, $options = null, $type = null)
    {
        $this->object = new Field\DualSelect($name, $options, $type);
        $this->notify('createFieldDualSelect');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @return Field\File
     */
    public function createFieldFile($name)
    {
        $this->object = new Field\File($name);
        $this->notify('createFieldFile');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @return Field\File
     */
    public function createFieldFileMultiple($name)
    {
        $this->object = new Field\FileMultiple($name);
        $this->notify('createFieldFileMultiple');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @param null $event
     * @return Field\File
     * @deprecated Use createFieldFile(). Remove for ver 2.0
     */
    public function createFieldFileModel($name, $event = null)
    {
        $this->object = new Field\FileModel($name, $event);
        $this->notify('createFieldFileModel');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @return Field\GmapSelect
     */
    public function createFieldGmapSelect($name)
    {
        $this->object = new Field\GmapSelect($name);
        $this->notify('createFieldGmapSelect');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @param Type\Iface $type
     * @return Field\Hidden
     */
    public function createFieldHidden($name, $type = null)
    {
        $this->object = new Field\Hidden($name, $type);
        $this->notify('createFieldHidden');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @param mixed $mce
     * @return Field\Mce
     * @todo This field need the jscripts to be re-located to the assets libs
     */
    public function createFieldMce($name, $mce = null)
    {
        $this->object = new Field\Mce($name, $mce);
        $this->notify('createFieldMce');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @return Field\Money
     */
    public function createFieldMoney($name)
    {
        $this->object = new Field\Money($name);
        $this->notify('createFieldMoney');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @return Field\Password
     */
    public function createFieldPassword($name)
    {
        $this->object = new Field\Password($name);
        $this->notify('createFieldPassword');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @param array $options
     * @param Type\Iface $type
     * @return Field\RadioGroup
     */
    public function createFieldRadioGroup($name, $options = null, $type = null)
    {
        $this->object = new Field\RadioGroup($name, $options, $type);
        $this->notify('createFieldRadioGroup');
        return $this->object;
    }

    /**
     * Create
     *
     * @param string $name
     * @param int $min
     * @param int $max
     * @param int $step
     * @param Type\Iface $type
     * @return Field\Range
     *
     * @note New HTML5 Element
     */
    public function createFieldRange($name, $min = -10, $max = 10, $step = 1, $type = null)
    {
        $this->object = new Field\Range($name, $min, $max, $step, $type);
        $this->notify('createFieldRange');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @param mixed $renderer
     * @return Field\Renderer
     */
    public function createFieldRenderer($name, $renderer)
    {
        $this->object = new Field\Renderer($name, $renderer);
        $this->notify('createFieldRenderer');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @param array $options
     * @param Type\Iface $type
     * @return Field\Select
     */
    public function createFieldSelect($name, $options = null, $type = null)
    {
        $this->object = new Field\Select($name, $options, $type);
        $this->notify('createFieldSelect');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @param array $options
     * @return Field\SelectMulti
     */
//    public function createFieldSelectChosen($name, $options = null)
//    {
//        $this->object = new Field\SelectChosen($name, $options);
//        $this->notify('createFieldSelectChosen');
//        return $this->object;
//    }

    /**
     * create
     *
     * @param string $name
     * @param array $options
     * @param null $width
     * @return Field\SelectMulti
     */
    public function createFieldSelectMulti($name, $options = null, $width = null)
    {
        $this->object = new Field\SelectMulti($name, $options, null, $width);
        $this->notify('createFieldSelectMulti');
        return $this->object;
    }

    /**
     * create
     *
     * @param string $name
     * @param array $options
     * @return Field\Spinner
     */
    public function createFieldSpinner($name, $options = array())
    {
        $this->object = new Field\Spinner($name, $options);
        $this->notify('createFieldSpinner');
        return $this->object;
    }

    /**
     * Create
     *
     * @param string $name
     * @param Type\Iface $type
     * @return Field\Text
     */
    public function createFieldText($name, $type = null)
    {
        $this->object = new Field\Text($name, $type);
        $this->notify('createFieldText');
        return $this->object;
    }

    /**
     * Create
     *
     * @param string $name
     * @return Field\Textarea
     */
    public function createFieldTextarea($name)
    {
        $this->object = new Field\Textarea($name);
        $this->notify('createFieldTextarea');
        return $this->object;
    }


    /**
     * create
     *
     * @param string $name
     * @return Field\TimezoneSelect
     */
    public function createFieldTimezoneSelect($name)
    {
        $this->object = new Field\TimezoneSelect($name);
        $this->notify('createFieldTimezoneSelect');
        return $this->object;
    }




}
