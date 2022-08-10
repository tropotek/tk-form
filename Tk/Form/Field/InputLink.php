<?php
namespace Tk\Form\Field;


/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class InputLink extends Input
{

    protected $copyEnabled = false;

    /**
     * @return bool
     */
    public function isCopyEnabled()
    {
        return $this->copyEnabled;
    }

    /**
     * @param bool $copyEnabled
     * @return InputLink
     */
    public function setCopyEnabled($copyEnabled = true)
    {
        $this->copyEnabled = $copyEnabled;
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

        if ($this->isCopyEnabled())
            $template->setVisible('copy');

        $js = <<<JS
jQuery(function ($) {
  $('.tk-input-link').each(function () {
    var input = $(this).find('input');
    if (input.val() === '') {
      //$(this).find('button').attr('disabled', 'disabled').addClass('disabled');
      $(this).find('.input-group-btn').hide();
    } else {
      $(this).find('button.btn-lnk').on('click', function () {
        window.open(input.val());
      });
      $(this).find('button.btn-cpy').on('click', function () {
        input.focus().select();
        document.execCommand('copy');
        input.blur().prop('selectionStart', 0).prop('selectionEnd',0);
        return false;
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
    <button class="btn btn-default btn-cpy" type="button" title="Click to copy the link." choice="copy"><i class="fa fa-copy"></i></button>
    <button class="btn btn-default btn-lnk" type="button" title="Click to view the URL in a new window."><i class="fa fa-link"></i></button>
  </span>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }
    
    
    
}