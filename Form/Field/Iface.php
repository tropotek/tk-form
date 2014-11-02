<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 * Iface
 *
 *
 * @package \Form\Field
 */
class Iface extends \Form\Element
{
    /**
     * This will convert the value to the required data type
     * @var \Form\Type\Iface
     */
    protected $type = null;


    /**
     * If this is false the field value will not be loaded during the
     * Form method calls: loadObject() and getValuesArray()
     *
     * @var bool
     */
    protected $loadable = true;

    /**
     * @var string
     */
    protected $tabGroup = '';

    /**
     * @var string
     */
    protected $fieldset = '';

    /**
     * @var string
     */
    protected $placeholder = '';

    /**
     * @var bool
     */
    protected $autocomplete = true;

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * @var bool
     */
    protected $readonly = false;

    /**
     * @var int
     */
    protected $maxlength = 0;

    /**
     * @var string
     */
    protected $accessKey = '';

    /**
     * @var string
     */
    protected $fieldWrapper = true;

    /**
     * Tab indexes of <= 0 are ignored
     * @var int
     */
    protected $tabindex = -1;

    /**
     * @var string
     */
    protected $fieldClassList = array();





    /**
     * __construct
     *
     * @param string $name
     * @param \Form\Type\Iface $type
     */
    public function __construct($name, $type = null)
    {
        $this->setName($name);
        $this->setLabel(self::makeLabel($name));
        $this->setType($type);
        if (!$this->getType()) {
            $this->setType(new \Form\Type\String());
        }
        // For Bootstrap....GRRR
        $this->addCssClass('form-control');
    }




    /**
     * Get the field's value object
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->type->getValue();
    }

    /**
     * Set the value of the element from a mixed type
     *
     * @param mixed $value
     * @return \Form\Field
     */
    public function setValue($value)
    {
        $this->type->setValue($value);
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
        return false;
    }

    /**
     * Override this method if you have a field that comprises
     * of more than one field. And you wand each field to be set
     * in the output object or array
     *
     * @see DateSet
     * @return bool
     */
    public function isMultiField()
    {
        return false;
    }


    /**
     * If enabled the field renderer will render the outer field
     * template that contains erros, labels, notes etc
     * If disabled then the element will be rendered without the
     * field renderer template leaving only this fields objects template
     * Set to false for hidden fields.
     *
     * @param bool|\Form\Field\type $b
     * @return \Form\Field\Iface
     */
    public function enableFieldWrapper($b = true)
    {
        $this->fieldWrapper = $b;
        return $this;
    }

    /**
     * Should the field renderer add the wrapper to this element?
     *
     * @return bool
     */
    public function hasFieldWrapper()
    {
        return $this->fieldWrapper;
    }

    /**
     * Get the field's placeholder value
     *
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * Set Placeholder string
     *
     * @param string $value
     * @return \Form\Field\Iface
     */
    public function setPlaceholder($value)
    {
        $this->placeholder = $value;
        return $this;
    }


    /**
     * Set the tab group name of this field
     *
     * @param string $str
     * @return \Form\Field\Iface
     */
    public function setTabGroup($str)
    {
        $this->tabGroup = $str;
        return $this;
    }

    /**
     * Get the tab group name of this field
     *
     * @return string
     */
    public function getTabGroup()
    {
        return $this->tabGroup;
    }


    /**
     * Set the fieldset group name of this field
     *
     * @param string $str
     * @return \Form\Field\Iface
     */
    public function setFieldset($str)
    {
        $this->fieldset = $str;
        return $this;
    }

    /**
     * Get the fieldset group name of this field
     *
     * @return string
     */
    public function getFieldset()
    {
        return $this->fieldset;
    }









    /**
     * Get the form CSS class
     *
     * @param string $className
     * @return string
     */
    public function addFieldClass($className)
    {
        $this->fieldClassList[$className] = $className;
        return $this;
    }

    /**
     *
     * @param string $className
     * @return $this
     */
    public function deleteFieldClass($className)
    {
        unset($this->fieldClassList[$className]);
        return $this;
    }

    /**
     * Get the wrapper field class list
     *
     * @return array
     */
    public function getFieldClassList()
    {
        return $this->fieldClassList;
    }

    /**
     * Clear the style class list
     *
     * @return \Form\Element
     */
    public function clearFieldClassList()
    {
        $this->fieldClassList = array();
        return $this;
    }

    /**
     * Set the autocomplete state of this field
     *
     * @param bool $b
     * @return \Form\Field\Iface
     */
    public function setAutocomplete($b = true)
    {
        $this->autocomplete = ($b == true);
        return $this;
    }

    /**
     * set the value type object
     *
     * @param \Form\Type\Iface $type
     * @return \Form\Field\Iface
     */
    public function setType($type)
    {
        if ($type instanceof \Form\Type\Iface) {
            $this->type = $type;
            $this->type->setField($this);
        }
        return $this;
    }

    /**
     * Return the type converter object
     *
     * @return \Form\Type\Iface
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * If this is false the field value will not be loaded during the
     * Form method calls: loadObject() and getValuesArray()
     *
     * @param bool $b
     */
    public function setLoadable($b = true)
    {
        $this->loadable = ($b === true);
    }

    /**
     * Get the field loadable status
     * @return bool
     */
    public function isLoadable()
    {
        return $this->loadable;
    }

    /**
     * Set the readonly state of this field
     *
     * @param bool $b
     * @return \Form\Field\Iface
     */
    public function setReadonly($b = true)
    {
        $this->readonly = ($b == true);
        return $this;
    }

    /**
     * Returns true if the field is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->readonly;
    }

    /**
     * Set the field required state
     *
     * @param bool $b
     * @return \Form\Field\Iface
     */
    public function setRequired($b = true)
    {
        $this->required = ($b === true);
        return $this;
    }

    /**
     * Is the field a required field
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Set the size, limit the number of characters in a standard text input field
     *
     * @param int $i
     * @return \Form\Field\Iface
     */
    public function setMaxlength($i)
    {
        $this->maxlength = (int)$i;
        return $this;
    }

    /**
     * Set the access key
     * EG 'a' = ALT+a
     *
     * @param string $char
     */
    public function setAccessKey($char)
    {
        $this->accessKey = substr($char, 0, 1);
    }

    /**
     * Get this field's acesskey if one exists.
     *
     * @return string
     */
    public function getAccessKey()
    {
        return $this->accessKey;
    }

    /**
     * Set the tab index of this field
     *
     * @param int $i
     */
    public function setTabIndex($i)
    {
        $this->tabIndex = (int)$i;
    }

    /**
     * Get the tab index order of this element if available.
     *
     * @return int
     */
    public function getTabIndex()
    {
        return $this->tabIndex;
    }








    /**
     * show
     */
    public function show()
    {
        $t = $this->getTemplate();

        // Render element var
        if (!$t->keyExists('var', 'element')) {
            return;
        }
        if (!$this->enabled) {
            $t->setAttr('element', 'disabled', 'disabled');
        }
//        if ($this->required && !$this->form->hasTabGroups()) {
//            $t->setAttr('element', 'required', 'required');
//        }
        if ($this->readonly) {
            $t->setAttr('element', 'readonly', 'readonly');
        }
        if (!$this->autocomplete) {
            $t->setAttr('element', 'autocomplete', 'off');
        }
        if ($this->getPlaceholder()) {
            $t->setAttr('element', 'placeholder', $this->getPlaceholder());
        }
        if ($this->accessKey) {
            $t->setAttr('element', 'accesskey', $this->accessKey);
        }
        if ($this->tabindex > 0) {
            $t->setAttr('element', 'tabindex', $this->tabindex);
        }
        
        foreach ($this->cssList as $v) {
            $t->addClass('element', $v);
        }

        foreach ($this->getAttrList() as $attr => $val) {
            $t->setAttr('element', $attr, $val);
        }

        $style = '';
        foreach ($this->getStyleList() as $name => $val) {
            $style .= $name . ':'.$val.';';
        }
        if ($style) {
            $t->setAttr('element', 'style', $style);
        }

        // Element
        $t->setAttr('element', 'name', $this->name);
        $t->setAttr('element', 'id', $this->getId());
        if ($t->getVarElement('element')->nodeName == 'input') {
            if ($this->maxlength > 0) {
                $t->setAttr('element', 'maxlength', $this->maxlength);
            }
            // Render a value
            $fieldValues = $this->getType()->getFieldValues();
            if (isset($fieldValues[$this->name]) && !is_array($fieldValues[$this->name])) {
                $t->setAttr('element', 'value', $fieldValues[$this->name]);
            }
        }

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
<input type="text" var="element" />
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }

}