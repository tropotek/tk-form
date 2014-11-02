<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 * A form radio group field.
 * The radio group is sent an array of options in the following format:
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
class RadioGroup extends Select
{

    /**
     * __construct
     *
     * @param string $name
     * @param array $options
     * @param \Form\Type\Iface $type
     */
    public function __construct($name, $options = null, $type = null)
    {
        parent::__construct($name, $options, $type);
        $this->setOptions($options);
        $this->subFieldValues[$this->name] = array();
        $this->clearCssClassList();
    }



    /**
     * Render the widget.
     *
     */
    public function show()
    {
        $t = $this->getTemplate();
        foreach ($this->options as $i => $arr) {
            if (!is_array($arr)) {
                continue;
            }
            $row = $t->getRepeat('row');
            $row->setAttr('element', 'name', $this->name);
            $row->setAttr('element', 'id', $this->getId().'_'.$i);
            $row->setAttr('element', 'value', $arr[1]);
            $row->setAttr('label', 'for', $this->getId().'_'.$i);
            $row->insertText('labelText', strip_tags(trim($arr[0])) );
            if ($this->isSelected($arr[1])) {
                $row->setAttr('element', 'checked', 'checked');
                $t->setAttr('label', 'for', $this->getId().'_'.$i);
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
        $value = $this->type->getFieldValues();
        if (!array_key_exists($this->name, $value)) {
            return false;
        }
        if ($value[$this->name] instanceof \Tk\Db\ArrayObject) {
            foreach ($value[$this->name] as $obj) {
                if ($obj->id == $val) return true;
            }
        } else {
            if ($value && in_array($val, $value)) {
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
  <label for="" var="label row" repeat="row" class="radio">
    <input type="radio" var="element" />
    <span var="labelText"></span>
  </label>
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }


}