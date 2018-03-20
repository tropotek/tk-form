<?php
namespace Tk\Form\Field;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 *
 * @see http://www.w3schools.com/tags/tag_option.asp
 */
class Option
{
    use \Tk\Dom\AttributesTrait;
    use \Tk\Dom\CssTrait;

//    /**
//     * @var array
//     */
//    protected $attrList = array();
//
//    /**
//     * @var array
//     */
//    protected $cssList = array();




    /**
     * @var bool
     */
    protected $disabled = false;

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var string
     */
    protected $value = '';

    /**
     * @var string
     */
    protected $text = '';


    /**
     * @param string $text
     * @param string $value
     * @param bool $disabled
     * @param string $label
     */
    public function __construct($text, $value = '', $disabled = false, $label = '')
    {
        $this->text = $text;
        $this->value = $value;
        $this->disabled = $disabled;
        $this->label = $label;
    }

    /**
     * Create an Option object
     * 
     * @param $text
     * @param string $value
     * @param bool|false $disabled
     * @param string $label
     * @return Option
     */
    static function create($text, $value = '', $disabled = false, $label = '') 
    {
        $opt = new self($text, $value, $disabled, $label);
        return $opt;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Specifies the value to be sent to a server
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Specifies that an option that should be disabled
     *
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * Specify an option that should be disabled
     *
     * @param $b
     * @return $this
     */
    public function setDisabled($b)
    {
        $this->disabled = $b;
        return $this;
    }

    /**
     * Specifies a shorter label for an option
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }








//    /**
//     * Add an attribute to the element node
//     *
//     * @param $attrName
//     * @param $value
//     * @return $this
//     */
//    public function setAttr($attrName, $value = '')
//    {
//        if (!$value) $value = $attrName;
//        $this->attrList[$attrName] = $value;
//        return $this;
//    }
//
//    /**
//     * Remove an attribute from the element node
//     *
//     * @param $attrName
//     * @return $this
//     */
//    public function removeAttr($attrName)
//    {
//        if (isset($this->attrList[$attrName])) {
//            unset($this->attrList[$attrName]);
//        }
//        return $this;
//    }
//
//    /**
//     * Get an attribute from this node
//     * NOTE: You can only retrieve attributes that have been set via setAttr()
//     *
//     * @param string $attrName
//     * @return string|null
//     */
//    public function getAttr($attrName)
//    {
//        if (isset($this->attrList[$attrName])) {
//            return $this->attrList[$attrName];
//        }
//    }
//
//    /**
//     * Get the attribute list
//     *
//     * @return array
//     */
//    public function getAttrList()
//    {
//        return $this->attrList;
//    }
//
//    /**
//     * Set the attributes list array
//     *
//     * If no parameter given the list is cleared
//     *
//     * @param array $array
//     * @return $this
//     */
//    public function setAttrList($array = array())
//    {
//        $this->attrList = $array;
//        return $this;
//    }
//
//    /**
//     * Add a CSS Class name to the node
//     *
//     * @param string $className
//     * @return $this
//     */
//    public function addCss($className)
//    {
//        $this->cssList[$className] = $className;
//        return $this;
//    }
//
//    /**
//     * Remove a CSS Class name from the node
//     *
//     * @param string $className
//     * @return $this
//     */
//    public function removeCss($className)
//    {
//        unset($this->cssList[$className]);
//        return $this;
//    }
//
//    /**
//     * Set the CSS class list array
//     *
//     * If no parameter given the list is cleared
//     *
//     * @param array $array
//     * @return $this
//     */
//    public function setCssList($array = array())
//    {
//        $this->cssList = $array;
//        return $this;
//    }
//
//    /**
//     * Get the CSS class style list for this element
//     *
//     * @return array
//     */
//    public function getCssList()
//    {
//        return $this->cssList;
//    }
}