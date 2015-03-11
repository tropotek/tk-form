<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 * A form single option select box.
 * Only one option can be selected.
 *
 * The select box is sent an array of options in the following format:
 * <code>
 *   $options = array(
 *     array('name1', 'value 1'),
 *     array('name2', 'value 2'),
 *     ...
 *   );
 * </code>
 *
 * Optionally an array of the following format can be used:
 * <code>
 *   $options = array('value1' => 'name1', 'value2' => 'name2', ...);
 * </code>
 *
 * @package Form\Field
 */
class SelectMulti extends Select
{

    protected $size = 8;
    protected $width = null;

    protected $filter = false;


    public function __construct($name, $options = null, $type = null, $width = null)
    {
        $this->width = $width;
        parent::__construct($name, $options, $type);
    }

    /**
     * enableFilter
     *
     * @param bool $b
     * @return $this
     */
    public function enableFilter($b = true)
    {
        $this->filter = $b;
        return $this;
    }


    /**
     * Does this field return data as an array.
     * This will happen when the field name ends in '[]'
     * So this can happen for multiple checkboxes and multi select lists etc...
     *
     * @return bool
     */
    public function hasArrayData()
    {
        return true;
    }

    /**
     * Set the number of rows to display in the select field.
     *
     * @param int $i
     */
    public function setSize($i)
    {
        if ($i < 1) $i = 1;
        if ($i > 100) $i = 100;
        $this->size = $i;
    }


    /**
     * Render the widget.
     *
     */
    public function show()
    {
        parent::show();
        $t = $this->getTemplate();
        $t->setAttr('element', 'multiple', 'multiple');
        $t->setAttr('element', 'name', $this->name.'[]');
    }


    /**
     * Compare a value and see if it si selected.
     *
     * @param string $val
     * @return bool
     */
    protected function isSelected($val)
    {
        $values = $this->type->getFieldValues();
        if (!isset($values[$this->name])) return false;
        if ($values[$this->name] instanceof \Tk\Db\ArrayObject) {
            foreach ($values[$this->name] as $obj) {
                if ($obj->id == $val) return true;
            }
        } else {
            if ($values && in_array($val, $values[$this->name])) {
                return true;
            }
        }
        return false;
    }


    /**
     * makeTemplate
     *
     * @return string
     */
    public function __makeTemplate()
    {
        $xmlStr = <<<XML
<?xml version="1.0"?>
<div>
<select var="element">
  <option value="" repeat="option" var="option"></option>
  <optgroup label="" repeat="optgroup" var="optgroup"><option value="" repeat="option" var="option"></option></optgroup>
</select>
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }


}