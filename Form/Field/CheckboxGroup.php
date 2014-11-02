<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 * A form checkbox group field.
 * The checkbox group is sent an array of options in the following format:
 * <code>
 *   $options = array(
 *     array('name1', 'value 1'),
 *     array('name2', 'value 2')
 *   );
 * </code>
 *
 *
 * @package Form\Field
 */
class CheckboxGroup extends Select
{

    /**
     *
     *
     * @param string $name
     * @param array $options
     * @param null $type
     */
    public function __construct($name, $options = null, $type = null)
    {
        parent::__construct($name, $type);
        $this->setOptions($options);
        $this->clearCssClassList();
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
     * Render the widget.
     *
     * @throws \Tk\Exception
     * @return \Dom\Template|string|void
     */
    public function show()
    {
        $t = $this->getTemplate();

        foreach ($this->options as $i => $arr) {
            if (!is_array($arr) && (!$this->options[0] instanceof \Form\SelectInterface)) {
                continue;
            }
            if (!$arr instanceof \Tk\Db\Model) {
                if (!isset ($arr[1])) {
                    throw new \Tk\Exception('please use an array like array(`name`, `value`) not array(`name` => `value`)...');
                }
                $name = $arr[0];
                $value = $arr[1];
            } else {
                $name = $arr->getSelectText();
                $value = $arr->getSelectValue();
            }

            $row = $t->getRepeat('row');
            $row->addClass('row', htmlentities($value));
            $row->setAttr('element', 'name', $this->name . '[]');
            $row->setAttr('element', 'id', $this->getId().'_'.$i);
            $row->setAttr('element', 'value', $value);
            $row->setAttr('label', 'for', $this->getId().'_'.$i);
            $row->insertText('labelText', strip_tags(trim($name)) );

            if ($this->isSelected($value)) {
                $row->setAttr('element', 'checked', 'checked');
                $t->setAttr('label', 'for', $this->getId().'_'.$i);
            }
            if ($this->isReadonly()) {
                $row->setAttr('element', 'readonly', 'readonly');
            }
            if (!$this->isEnabled()) {
                $row->setAttr('element', 'disabled', 'disabled');
            }
            $row->appendRepeat();
        }

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
        if (!array_key_exists($this->name, $values)) {
            return false;
        }
        if (count($values[$this->name]) && $values[$this->name][0] instanceof \Form\SelectInterface) {
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
<div class="radioGroup">
  <label repeat="row" var="row label" class="checkbox">
    <input type="checkbox" var="element" />
    <span var="labelText"></span>
  </label>
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }

}