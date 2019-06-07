<?php
namespace Tk\Form\Field;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class InputLink extends Input
{

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        $js = <<<JS
jQuery(function ($) {
  $('.tk-input-link').each(function () {
    var input = $(this).find('input');
    if (input.val() === '') {
      $(this).find('.input-group-btn').hide();
    } else {
      $(this).find('button').on('click', function () {
        window.open(input.val());
      });
    }
  });
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
<div class="input-group tk-input-link">
  <input type="text" var="element" class="form-control" />
  <span class="input-group-btn">
    <button class="btn btn-default" type="button" title="Click to view the URL in a new window"><i class="fa fa-link"></i></button>
  </span>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }
    
    
    
}