<?php
namespace Tk\Form\Field;


use Tk\Callback;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Autocomplete extends Input
{
    /**
     * @var Callback
     */
    protected $onAjax = null;

    /**
     * Autocomplete constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->onAjax = Callback::create();
        parent::__construct($name);
        $this->addCss('tk-autocomplete');
    }

    public function load($values)
    {
        $request = $this->getForm()->getRequest();
        if ($request->has('a')) {
            $data = $this->getOnAjax()->execute($this);
            if (is_array($data) | is_object($data)) {
                \Tk\ResponseJson::createJson($data)->send();
                exit();
            }
        }
        return parent::load($values);
    }

    /**
     * @return Callback
     */
    public function getOnAjax()
    {
        return $this->onAjax;
    }

    /**
     * the callback should return an array of name/value pairs to use for the autocomplete
     *  function ($autocomplete): array|object { }
     *
     * @param callable $callable
     * @param int $priority
     * @return $this
     */
    public function addOnAjax($callable, $priority = Callback::DEFAULT_PRIORITY)
    {
        $this->getOnAjax()->append($callable, $priority);
        return $this;
    }

    /**
     * the callback should return an array of name/value pairs to use for the autocomplete
     *
     * function ($request, $field): array|object { } deprecated $request removed
     *
     * @param callable|null $callable
     * @return Autocomplete
     * @deprecated use $this->addOnAjax($callable, $priority)
     */
    public function setOnAjax(?callable $callable)
    {
        $this->addOnAjax($callable);
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
        $js = <<<JS
jQuery(function ($) {
  
  if (typeof $.autocomplete === 'undefined') {
    return;
  }
  
  var cache = {};
  $('input.tk-autocomplete[type=text]').autocomplete({
    minLength: 2,
    source: function (request, response) {
      var term = request.term;
      if (term in cache) {
        response(cache[term]);
        return;
      }

      request.a = 'a';
      $.getJSON(window.document.location.href, request, function (data, status, xhr) {
        cache[term] = data;
        response(data);
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
<input type="text" var="element" class="form-control" />
HTML;
        return \Dom\Loader::load($xhtml);
    }
    
    
    
}