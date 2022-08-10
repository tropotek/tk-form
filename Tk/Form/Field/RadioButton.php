<?php
namespace Tk\Form\Field;


/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class RadioButton extends Radio
{

    /**
     * @var string
     */
    protected $text = null;


    /**
     * Item constructor.
     *
     * @param string $name
     * @param string $text
     * @throws \Tk\Form\Exception
     */
    public function __construct($name, $text = '')
    {
        parent::__construct($name);
        $this->text = $text;
        $this->onShowOption = array($this, 'showOption');
    }

    /**
     * @param \Dom\Template $template
     * @param \Tk\Form\Field\Option $option
     * @param string $var
     */
    public function showOption($template, $option, $var = 'option')
    {
        $checkedSet = false;        // TODO: Should this default to true/false

        // allow only one radio to be selected.
        if ($this->isSelected($option->getValue()) && !$checkedSet) {
            $template->addCss('label', 'active');
        }
        if ($option->getCssString()) {
            $template->addCss('icon', $option->getCssString());
            $template->setVisible('icon');
        }
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
        if ($value !== null && $value == $val) {
            return true;
        }
        return false;
    }


    public function load($values)
    {
        parent::load($values);
        return $this;
    }

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function show()
    {
        $template = parent::show();
        $template->insertText('text', $this->text);
        return $template;
    }

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="tk-radio-button">
  <p class="" var="text"></p>
  <div class="radio" data-toggle="buttons">
    <label class="btn btn-default btn-sm" repeat="option" var="option label"><i class="" var="icon" choice="icon"></i><span var="text"></span><input type="radio" var="element" autocomplete="off" class="hide" /></label>
  </div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }
    
    
}