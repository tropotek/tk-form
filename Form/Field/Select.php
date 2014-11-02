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
 * Optionaly an array of the following format can be used:
 * <code>
 *   $options = array('value1' => 'name1', 'value2' => 'name2', ...);
 * </code>
 *
 * @package \Form\Field
 */
class Select extends Iface
{

    /**
     * @var array
     */
    protected $optgroups = array();

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var array
     */
    protected $prependOptions = array();



    public function __construct($name, $options = null, $type = null)
    {
        parent::__construct($name, $type);
        $this->setOptions($options);
        $this->subFieldValues[$name] = array();
    }

    /**
     * Append an option to the slect element
     *
     * @param string $name
     * @param string $value
     * @param bool $disabled
     * @return $this
     */
    public function prependOption($name, $value, $disabled = false)
    {
        $this->prependOptions[] = array($name, $value, $disabled);
        return $this;
    }

    /**
     * Append an option to the slect element
     *
     * @param string $name
     * @param string $value
     * @param string $optgroup
     * @param bool $disabled
     * @return $this
     */
    public function appendOption($name, $value, $optgroup = '', $disabled = false)
    {
        if ($optgroup) {
            if (!isset($this->optgroups[$optgroup])) {
                $this->optgroups[$optgroup] = array();
            }
            $this->optgroups[$optgroup][] = array($name, $value, $disabled);
        } else {
            $this->options[] = array($name, $value, $disabled);
        }
        return $this;
    }

    /**
     * Set the options array
     * The option array is in the format of array(array('name' => 'value'), array('name', 'value'),  etc...);
     * this format allows for duplicate name and values
     *
     * @param $options
     * @param string $optgroup
     * @return $this
     */
    public function setOptions($options, $optgroup = '')
    {
        if (isset($options[0]) && $options[0] instanceof \Form\SelectInterface) {
            foreach ($options as $o) {
                if ($o->getSelectValue() instanceof \Tk\Db\ArrayObject) {
                    $this->setOptions($o->getSelectValue(), $o->getSelectText());
                } else {
                    $this->appendOption($o->getSelectText(), $o->getSelectValue(), $optgroup);
                }
            }
        } else if (is_array($options)) {
            if (is_array(current($options)) && !$optgroup) { // array(array('name', 'val'), array('name2' => 'val2'), .....)
                $this->options = $options;
            } else {  // array(1 => 'name', 'val' => 'name2', ...)
                foreach ($options as $k => $v) {
                    if (is_array($v)) {
                        $this->setOptions($v, $k);
                    } else {
                        $this->appendOption($v, $k, $optgroup);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Clear the options array
     */
    public function clearOptions()
    {
        $this->options = array();
        return $this;
    }

    /**
     * getOptgroups
     *
     * @return array
     */
    public function getOptgroups()
    {
        return $this->optgroups;
    }

    /**
     * getOptions
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * getPrependOptions
     *
     * @return array
     */
    public function getPrependOptions()
    {
        return $this->prependOptions;
    }


    /**
     * Render the widget.
     *
     * @return \Dom\Template|string|void
     */
    public function show()
    {
        parent::show();
        $t = $this->getTemplate();

        foreach ($this->prependOptions as $arr) {
            if (!is_array($arr)) {
                continue;
            }
            $row = $t->getRepeat('option');
            $row->setAttr('option', 'value', $arr[1]);
            $row->insertText('option', trim($arr[0]));
            if ($this->isSelected($arr[1])) {
                $row->setAttr('option', 'selected', 'selected');
            }
            if (isset($arr[2]) && $arr[2]) {
                $row->setAttr('option', 'disabled', 'disabled');
            }
            $row->appendRepeat();
        }
        
        foreach ($this->options as $arr) {
            if (!is_array($arr)) {
                continue;
            }
            $row = $t->getRepeat('option');
            $row->setAttr('option', 'value', $arr[1]);
            $row->insertText('option', trim($arr[0]));

            if ($this->isSelected($arr[1])) {
                $row->setAttr('option', 'selected', 'selected');
            }
            if (isset($arr[2]) && $arr[2]) {
                $row->setAttr('option', 'disabled', 'disabled');
            }
            $row->appendRepeat();
        }

        foreach ($this->optgroups as $groupName => $arr) {
            if (!is_array($arr)) {
                continue;
            }
            $group = $t->getRepeat('optgroup');
            $group->setAttr('optgroup', 'label', $groupName);
            foreach ($arr as $arr2) {
                if (!is_array($arr2)) {
                    continue;
                }
                $row2 = $group->getRepeat('option');
                $row2->setAttr('option', 'value', $arr2[1]);
                $row2->insertText('option', '  ' . trim($arr2[0]));
                if ($this->isSelected($arr2[1])) {
                    $row2->setAttr('option', 'selected', 'selected');
                }
                if (isset($arr[2]) && $arr[2]) {
                    $row2->setAttr('option', 'disabled', 'disabled');
                    $row2->setAttr('option', 'selected', 'selected');
                }
                $row2->appendRepeat();
            }
            $group->appendRepeat();
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
        if (isset($values[$this->name]) && $values[$this->name] === $val) {
            return true;
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
<select var="element">
  <option value="" repeat="option" var="option"></option>
  <optgroup label="" repeat="optgroup" var="optgroup"><option value="" repeat="option" var="option"></option></optgroup>
</select>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }
}