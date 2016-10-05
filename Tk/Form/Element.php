<?php
namespace Tk\Form;

use Tk\Form;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
abstract class Element implements \Tk\InstanceKey
{

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var Form
     */
    protected $form = null;

    /**
     * @var array
     */
    protected $errors = array();
    
    /**
     * @var array
     */
    protected $paramList = null;

    /**
     * @var array
     */
    protected $attrList = array();

    /**
     * @var array
     */
    protected $cssClassList = array();

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var string
     */
    protected $notes = null;



    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    abstract public function getHtml();


    /**
     * Set the name for this element
     *
     *
     * @param $name
     * @return $this
     * @throws Exception
     */
    public function setName($name)
    {
        $this->name = $name;
        if (!$this->getLabel()) {
            $this->setLabel(self::makeLabel($this->getName()));
        }
        return $this;
    }

    /**
     * Get the unique name for this element
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    
    /**
     * Create a label from a name string
     * The default label uses the name (EG: `fieldNameSelect` -> `Field Name Select`)
     *
     * @param string $name
     * @return string
     */
    static function makeLabel($name)
    {
        $label = $name;
        $label = preg_replace_callback('/_([a-zA-Z_])/', function ($match) {    // Handle underscores
            return strtoupper($match[1]);
        }, $label);
        $label = ucfirst(preg_replace('/[A-Z_]/', ' $0', $label));
        $label = preg_replace('/(\[\])/', '', $label);
        if (substr($label, -2) == 'Id') {
            $label = substr($label, 0, -3);
        }
        return $label;
    }

    /**
     * Get the unique name for this field
     *
     * @param string $prepend
     * @return string
     */
    protected function makeId($prepend = 'fid_')
    {
        if ($this->getForm() && $prepend == 'fid_') {
            $prepend = $this->getForm()->getId() . '_';
        }
        return $prepend . $this->getName();
    }
    
    /**
     * Get a parameter from the array
     *
     * @param $name
     * @return bool
     */
    public function getParam($name)
    {
        if (!empty($this->paramList[$name])) {
            return $this->paramList[$name];
        }
        return false;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setParam($name, $value)
    {
        $this->paramList[$name] = $value;
        return $this;
    }

    /**
     * Get the param array
     *
     * @return array
     */
    public function getParamList()
    {
        return $this->paramList;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParamList($params)
    {
        $this->paramList = $params;
        return $this;
    }
    /**
     * Get the label of this field
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the label of this field
     *
     * @param $str
     * @return $this
     */
    public function setLabel($str)
    {
        $this->label = $str;
        return $this;
    }

    /**
     * Set the notes html
     *
     * @param string $html
     * @return $this
     */
    public function setNotes($html)
    {
        $this->notes = $html;
        return $this;
    }

    /**
     * Get any notes on this element
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set the form for this element
     *
     * @param Form $form
     * @return $this
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * Get the parent form element
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Add an error message html to the element
     *
     * @param string|array $msg
     * @return $this
     */
    public function addError($msg)
    {
        if (!is_array($msg)) {
            $msg = array($msg);
        }
        $this->errors = array_merge($this->errors, $msg);
        return $this;
    }

    /**
     * Get the element's error list as an array
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set the error array.
     * Overwrites the existing error array.
     *
     * @param array $errors
     * @return $this
     */
    public function setErrors($errors = array())
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Check if this element contains errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return (count($this->getErrors()) > 0);
    }

    /**
     * Add an attribute to the element node
     *
     * @param $attrName
     * @param $value
     * @return $this
     */
    public function setAttr($attrName, $value = '')
    {
        if (!$value) $value = $attrName;
        $this->attrList[$attrName] = $value;
        return $this;
    }

    /**
     * Get an attribute from this node
     * NOTE: You can only retrieve attributes that have been set via setAttr()
     *
     * @param string $attrName
     * @return string|null
     */
    public function getAttr($attrName)
    {
        if (isset($this->attrList[$attrName])) {
            return $this->attrList[$attrName];
        }
    }

    /**
     * Remove an attribute from the element node
     *
     * @param $attrName
     * @return $this
     */
    public function removeAttr($attrName)
    {
        if (isset($this->attrList[$attrName])) {
            unset($this->attrList[$attrName]);
        }
        return $this;
    }

    /**
     * Get the attribute list
     *
     * @return array
     */
    public function getAttrList()
    {
        return $this->attrList;
    }

    /**
     * Set the attributes list array
     *
     * If no parameter given the list is cleared
     *
     * @param array $array
     * @return $this
     */
    public function setAttrList($array = array())
    {
        $this->attrList = $array;
        return $this;
    }

    /**
     * Add a CSS Class name to the node
     *
     * @param string $className
     * @return $this
     */
    public function addCssClass($className)
    {
        $this->cssClassList[$className] = $className;
        return $this;
    }

    /**
     * Remove a CSS Class name from the node
     *
     * @param string $className
     * @return $this
     */
    public function removeCssClass($className)
    {
        unset($this->cssClassList[$className]);
        return $this;
    }

    /**
     * Set the CSS class list array
     *
     * If no parameter given the list is cleared
     *
     * @param array $array
     * @return $this
     */
    public function setCssClassList($array = array())
    {
        $this->cssClassList = $array;
        return $this;
    }

    /**
     * Get the CSS class style list for this element
     *
     * @return array
     */
    public function getCssClassList()
    {
        return $this->cssClassList;
    }

    /**
     * Create request keys with prepended string
     *
     * returns: `{instanceId}_{$key}`
     * 
     * The form->id is used as the instance key and must exist otherwise the key is returned unmodified.
     *
     * @param $key
     * @return string
     */
    public function makeInstanceKey($key)
    {
        if ($this->getForm()) {
            return $this->getForm()->getId() . '_' . $key;
        }
        return $key;
    }
}