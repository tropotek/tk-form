<?php
namespace Tk\Form\Renderer\Dom\Field;

use Tk\Form\Field;

/**
 * Class Date
 *
 *
 *
 *
 * @todo: get the date fromat from the DateTime field type object
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Date extends Iface
{


    /**
     * __construct
     *
     * @param Field\Iface $field
     */
    public function __construct(Field\Iface $field)
    {
        parent::__construct($field);
        $field->setAttr('placeholder', 'dd/mm/yyyy');
    }


    /**
     * The default element template
     *
     * Future HTML5 date type elements:
     *   <input type="month">
     *   <input type="week">
     *   <input type="time">
     *   <input type="datetime">
     *   <input type="datetime-local">
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<XHTML
<input type="date" var="element" class="form-control" />
XHTML;
        return \Dom\Loader::load($xhtml);
    }

}