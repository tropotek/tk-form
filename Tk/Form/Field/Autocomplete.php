<?php
namespace Tk\Form\Field;


/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Autocomplete extends Input
{
    /**
     * @var null|callable
     */
    protected $onAjax = null;

    /**
     * Autocomplete constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->addCss('tk-autocomplete');
    }

    public function load($values)
    {
        $request = $this->getForm()->getRequest();
        if ($request->has('a') && is_callable($this->getOnAjax())) {
            $data = call_user_func_array($this->getOnAjax(), array($request, $this));
            if (is_array($data) | is_object($data)) {
                \Tk\ResponseJson::createJson($data)->send();
                exit();
            }
        }
        return parent::load($values);
    }

    /**
     * @return callable|null
     */
    public function getOnAjax(): ?callable
    {
        return $this->onAjax;
    }

    /**
     * the callback should return an array of name/value pairs to use for the autocomplete
     *
     * function ($request, $field): array|object { }
     *
     * @param callable|null $callable
     * @return Autocomplete
     */
    public function setOnAjax(?callable $callable): Autocomplete
    {
        if (is_callable($callable)) {
            $this->onAjax = $callable;
        }
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