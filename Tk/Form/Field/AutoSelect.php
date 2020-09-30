<?php
namespace Tk\Form\Field;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 * @see https://select2.github.io/examples.html
 */
class AutoSelect extends Select
{

    /**
     * @var \Uni\Db\Course
     */
    protected $course = null;

    /**
     * @var \Dom\Template
     */
    protected $parentTemplate = null;

    /**
     * @var \App\Ui\Dialog\SupervisorCreate
     */
    protected $supervisorDialog = null;


    /**
     * @param \Uni\Db\Course $course
     * @param string $name
     * @param \Tk\Form\Field\Option\ArrayIterator|array $optionIterator
     * @throws \Tk\Form\Exception
     */
    public function __construct($course, $name, $optionIterator = null)
    {
        parent::__construct($name, $optionIterator);
        $this->course = $course;
    }

    /**select2.js
     * @param \Dom\Template $parentTemplate
     * @return $this
     * @throws \Exception
     * @throws \Tk\Form\Exception
     */
    public function enableSupervisorCreate($parentTemplate)
    {
        $this->parentTemplate = $parentTemplate;
        $this->supervisorDialog = new \App\Ui\Dialog\SupervisorCreate('Create ' .
            \App\Db\Phrase::findValue('supervisor', $this->course->getId()), $this->course);
        return $this;
    }

    /**
     * Compare a value and see if it is selected.
     *
     * @param string $val
     * @return bool
     */
    public function isSelected($val = '')
    {
        $value = $this->getValue();
        if (is_array($value) && in_array($val, $value)) return true;
        else if ($value !== null && $value == $val) return true;
        return false;
    }

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     * @throws \Dom\Exception
     */
    public function show()
    {
        $template = parent::show();

        $template->appendJsUrl(\Tk\Uri::create('/vendor/ttek/tk-form/js/select2/select2.js'));
        $template->appendCssUrl(\Tk\Uri::create('/vendor/ttek/tk-form/js/select2/select2-bootstrap4.css'));
        $template->appendJsUrl(\Tk\Uri::create('/vendor/ttek/tk-form/js/jquery.autoSelect.js'));

        $template->setAttr('element', 'data-ajax', \Tk\Uri::create('/ajax/supervisor/select2'));
        $template->setAttr('element', 'data-course-id', $this->course->getId());

        if ($this->supervisorDialog) {
            $template->setVisible('enableCreate');
            $template->setAttr('createBtn', 'data-modal-id', $this->supervisorDialog->getId());
            $this->parentTemplate->appendBodyTemplate($this->supervisorDialog->show());

            $js = <<<JS
jQuery(function ($) {
  // Open the supervisor select dialog on button click
  $('.tk-form').on('click', '.btn-create-supervisor', function (e) {
    $('#'+$(this).data('modalId')).modal('show');
  });
});
JS;
            $this->parentTemplate->appendJs($js);
        }

        return $template;
    }

    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="input-group select2-bootstrap-append" var="input-group">
  <select var="element" class="form-control tk-supervisor-select"><option repeat="option" var="option"></option></select>
  <span class="input-group-btn" choice="enableCreate">
    <button class="btn btn-default btn-create-supervisor" type="button" var="createBtn"><i class="fa fa-plus"></i></button>
  </span>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}