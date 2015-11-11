<?php
namespace Tk\Form;

use Tk\Form;

/**
 * Interface Element
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
abstract class Element
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
    protected $attrList = array();

    /**
     * @var array
     */
    protected $cssList = array();


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
     * Set the name for this element
     *
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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
    public function setAttr($attrName, $value)
    {
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
    public function addCss($className)
    {
        $this->cssList[$className] = $className;
        return $this;
    }

    /**
     * Remove a CSS Class name from the node
     *
     * @param string $className
     * @return $this
     */
    public function removeCss($className)
    {
        unset($this->cssList[$className]);
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
    public function setCssList($array = array())
    {
        $this->cssList = $array;
        return $this;
    }

    /**
     * Get the CSS class style list for this element
     *
     * @return array
     */
    public function getCssList()
    {
        return $this->cssList;
    }

}