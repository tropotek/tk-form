<?php
namespace Tk\Form\Field;


use Tk\Form\Field\Option\ArrayIterator;

/**
 * Allows for a dialog with a form to be added to a select field
 *    for creating new records and selecting in one field
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class DialogSelect extends \Tk\Form\Field\Select
{

    /**
     * @var \Tk\Ui\Dialog\Dialog|null
     */
    protected $dialog = null;


    /**
     * @param string $name
     * @param null|ArrayIterator|array|\Tk\Db\Map\ArrayObject $optionIterator
     * @param \Tk\Form|null $form
     * @param string $title
     */
    public function __construct($name, $optionIterator = null, $form = null, $title = '')
    {
        parent::__construct($name, $optionIterator);
        if ($form) {
            if (!$title)    // TODO: this may need a better auto naming system
                $title = 'Create ' . ucwords($name);
            $this->dialog = \Tk\Ui\Dialog\JsonForm::createJsonForm($form, $title);
        }
    }

    /**
     * @param string $name
     * @param null|ArrayIterator|array|\Tk\Db\Map\ArrayObject $optionIterator
     * @param \Tk\Form|null $form
     * @param string $title
     * @return static
     */
    public static function createDialogSelect($name, $optionIterator = null, $form = null, $title = '')
    {
        return new static($name, $optionIterator, $form, $title);
    }

    /**
     * Get the element HTML
     *
     * @return \Dom\Template
     * @throws \Exception
     */
    public function show()
    {
        $template = parent::show();

        // We appendToThe body as to avoid a nested form issue.
        $template->appendBodyTemplate($this->dialog->show());

        $template->setAttr('modelBtn', 'data-target', '#'.$this->dialog->getId());
        $template->setAttr('modelBtn', 'data-toggle', 'modal');
        $template->setAttr('modelBtn', 'title', $this->dialog->getTitle());

        $js = <<<JS
jQuery(function($) {
  $('.tk-json-form').on('DialogForm:submit', function (e, data) {
    // console.log('DialogForm:submit');
    // console.log(data);
    // Add the new contact/id to the select and select it
    var select = $('[data-target="#'+$(this).attr('id')+'"]').closest('.form-group').find('select');
    var opt = $('<option></option>');
    opt.text(data.name + ' (' + data.email + ')');
    opt.attr('value', data.id);
    select.append(opt);
    select.val(data.id);
    
  }).on('DialogForm:error', function (xhr) {
    // console.log('DialogForm:error');
    // console.log(xhr);
  });
  
});
JS;

        $template->appendJs($js);



        return $template;
    }

    /**
     * @return \Tk\Ui\Dialog\Dialog|null
     */
    public function getDialog(): ?\Tk\Ui\Dialog\Dialog
    {
        return $this->dialog;
    }

    /**
     * @param \Tk\Ui\Dialog\Dialog|null $dialog
     * @return DialogSelect
     */
    public function setDialog(?\Tk\Ui\Dialog\Dialog $dialog): DialogSelect
    {
        $this->dialog = $dialog;
        return $this;
    }

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="input-group tk-create-select">
  <select var="element" class="form-control">
    <option repeat="option" var="option"></option>
  </select>
  <span class="input-group-btn">
    <button type="button" class="btn btn-primary" title="" var="modelBtn"><i class="fa fa-plus"></i></button>
  </span>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}