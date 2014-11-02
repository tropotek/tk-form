<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 * This field resolves the value to a bit logic integer
 *
 * A form single option select box.
 * Only one option can be selected.
 *
 * The select box is sent an array of options in the following format:
 * <code>
 *   $options = array(
 *     array('name1', '2'),
 *     array('name2', '4'),
 *     array('name3', '8'),
 *     ...
 *   );
 * </code>
 *
 * Optionaly an array of the following format can be used:
 * <code>
 *   $options = array('2' => 'name1', '4' => 'name2', ...);
 * </code>
 *
 * @package Form\Field
 */
class CheckboxSelectBinary extends Select
{

    protected $size = 6;


    /**
     * @param string $name
     * @param null $options
     */
    public function __construct($name, $options = null)
    {
        parent::__construct($name, $options, new \Form\Type\Binary());
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
        return false;
    }

    /**
     * Set the number of row to display in the select field.
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
     * @param \Dom\Template $t
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
        $value = $values[$this->name];
        if (($val & $value) == $val) return true;
        return false;
    }

}