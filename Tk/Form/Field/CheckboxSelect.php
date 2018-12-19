<?php
namespace Tk\Form\Field;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class CheckboxSelect extends CheckboxGroup
{

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();

        $label = '';

        /* @var \Tk\Form\Field\Option $option */
        foreach($this->getOptions() as $option) {
            $tOpt = $template->getRepeat('option');

            if ($option->hasAttr('disabled')) {
                $tOpt->setAttr('option', 'disabled', 'disabled');
                $tOpt->setAttr('element', 'disabled', 'disabled');
            }
            $tOpt->insertText('text', $option->getName());

            $tOpt->setAttr('element', 'value', $option->getValue());
            $tOpt->setAttr('element', 'name', $this->getFieldName());
            
            if ($this->isSelected($option->getValue())) {
                $tOpt->setAttr('element', 'checked', 'checked');
                //$label .= $option->getName() . ', ';
            }

            // All other attributes
            $tOpt->setAttr('option', $option->getAttrList());
            $tOpt->addCss('option', $option->getCssList());

            foreach($this->getAttrList() as $key => $val) {
                if ($val == '' || $val == null) {
                    $val = $key;
                }
                $tOpt->setAttr('element', $key, $val);
            }

            // Element css class names
            foreach($this->getCssList() as $v) {
                $tOpt->addCss('element', $v);
            }
            
            $tOpt->appendRepeat();
        }

        //$label = rtrim($label, ', ');
        if (!$label) {
            $label = $this->getLabel();
            if ($this->getAttr('placeholder'))
                $label = $this->getAttr('placeholder');
        }
        $template->insertText('button', $label);

        $place = $this->getLabel();
        if ($this->getAttr('placeholder'))
            $place = $this->getAttr('placeholder');
        $template->setAttr('button', 'data-placeholder', $place);

        //$this->decorateElement($template, 'group');

        $js = <<<JS
jQuery(function (e) {
  
  function init() {
    var form = $(this);
    form.find('.checkbox-select').each(function () {
      var select = $(this);
      var btn = select.find('.dropdown-toggle');
      
      function updateButton() {
        var label = [];
        select.find(':checked').each(function () {
          label.push($(this).closest('label').find('span').text());
        });
        if (label.length) {
          btn.text(label.join(', '));
        } else {
          btn.text(btn.data('placeholder'));
        }
      }
      
      $(this).find('.dropdown-menu').on('click', function(e) {
        e.stopPropagation();
      });
      $(this).find('.dropdown-menu input').on('click', function(e) {
        //updateButton();
      });
    });
    
  }
  $('form').on('init', document, init).each(init);
});
JS;

        $template->appendJs($js);

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
<div class="dropdown checkbox-select" var="group">
  <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" var="button">
    <span var="value">-- Select --</span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuOffset" var="dropdown-menu">
      <li class="dropdown-item checkbox" repeat="option" var="option">
        <label var="label">
          <input type="checkbox" var="element" /> <span var="text"></span>
        </label>
      </li>
  </ul>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }
    
}