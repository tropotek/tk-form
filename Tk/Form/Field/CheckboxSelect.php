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

    protected $settings = array(
        'search' => true,
        'selectAll' => true
    );






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
        $template->insertText('value', $label);

        if ($this->settings['search'])
            $template->show('search');
        if ($this->settings['selectAll'])
            $template->show('select-all');

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
      var searchInput = select.find('.input-search');
      var clearBtn = select.find('.btn-clear');
      var selectAll = select.find('.select-all-checkbox');
      
      // This is to add the value to the label, but no good in situations with large data-sets
      // function updateButton() {
      //   var label = [];
      //   select.find(':checked').each(function () {
      //     label.push($(this).closest('label').find('span').text());
      //   });
      //   if (label.length) {
      //     btn.text(label.join(', '));
      //   } else {
      //     btn.text(btn.data('placeholder'));
      //   }
      // }
      // $(this).find('.dropdown-menu input').on('click', function(e) {
      //   //updateButton();
      // });
      
      $(this).find('.dropdown-menu').on('click', function(e) {
        e.stopPropagation();
      });
      
      clearBtn.on('click', function () {
        searchInput.val('');
        searchInput.trigger('keyup');
      });
      
      searchInput.on('keyup', function () {
        var terms = $(this).val();
        var list = select.find('.checkbox');
        if (terms === '') {
          list.show();
          return;
        }
        list.hide();
        list.filter(function () {
            return $(this).text().toLowerCase().indexOf(terms.toLowerCase()) >= 0;
        }).show();
      });
      
      selectAll.on('change', function () {
        if ($(this).prop('checked')) {
          select.find('.checkbox input').prop('checked', true);
        } else {
          select.find('.checkbox input').prop('checked', false);
        }
      });
      select.find('.checkbox input').on('change', function () {
        if (select.find('.checkbox input').length !== select.find('.checkbox input:checked').length) {
          selectAll.prop('checked', false);
        } else {
          selectAll.prop('checked', true);
        }
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
<div class="form-control dropdown checkbox-select" var="group">
  <button type="button" class="btn btn-light dropdown-toggle-off" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" var="button">
    <span><span var="value">-- Select --</span>  <i class="fa fa-caret-down"></i></span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuOffset" var="dropdown-menu">
      
      <li class="dropdown-item search" style="position: relative;" choice="search">
        <input type="text" class="input-search" style="width: 100%;" placeholder="Search..."/>
        <a href="#" class="btn-clear" style="position: absolute;top: 5px; right: 10px;"><i class="fa fa-times"></i></a>
      </li>
      <li class="dropdown-item select-all" choice="select-all">
        <label>
          <input type="checkbox" class="select-all-checkbox" /> <span>-- Select All --</span>
        </label>
      </li>
      
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