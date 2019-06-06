<?php
namespace Tk\Form\Field;


/**
 * NOTE: This object is designed for Bootstrap 4+ as previous versions it will not work as expected
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class InputGroup extends Input
{
    /**
     * @var array
     */
    protected $prependList = array();

    /**
     * @var array
     */
    protected $appendList = array();


    /**
     * EG:
     *   $field->prepend('<div class="input-group-text"><i class="fa fa-user"></i></div>');
     *
     * @param string|\Dom\Template $html
     */
    public function prepend($html)
    {
        $this->prependList[] = $html;
    }

    /**
     * EG:
     *   $field->append('<div class="input-group-text"><i class="fa fa-user"></i></div>');
     *
     * @param string|\Dom\Template $html
     */
    public function append($html)
    {
        $this->appendList[] = $html;
    }


    /**
     * @return string|\Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        foreach ($this->prependList as $html) {
            if ($html instanceof \Dom\Template) {
                $template->appendTemplate('prepend', $html);
            } else {
                $template->appendHtml('prepend', $html);
            }
            $template->setVisible('prepend');
        }

        foreach ($this->appendList as $html) {
            if ($html instanceof \Dom\Template) {
                $template->appendTemplate('append', $html);
            } else {
                $template->appendHtml('append', $html);
            }
            $template->setVisible('append');
        }

        return $template;
    }

    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="input-group">
  <div class="input-group-prepend" var="prepend" choice="prepend"></div>
  <input type="text" var="element" class="form-control" />
  <div class="input-group-append" var="append" choice="append"></div>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }
    
    
    
}