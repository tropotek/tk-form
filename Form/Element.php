<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form;

/**
 * A base object for all forms and its elements
 *
 *
 * @package \Form
 */
abstract class Element extends \Mod\Renderer
{

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var bool
     */
    protected $hidden = false;

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @var string
     */
    protected $attrList = array();

    /**
     * @var string
     */
    protected $styleList = array();

    /**
     * @var string
     */
    protected $cssList = array();

    /**
     * @var string
     */
    protected $notes = '';


    /**
     * @var Form
     */
    protected $form = null;

    /**
     * @var array
     */
    protected $errors = array();




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
        // Set the default Label
        $label = ucSplit($name);
        $label = preg_replace('/(\[\])/', '', $label);
        if (substr($label, -2) == 'Id') {
            $label = substr($label, 0, -3);
        }
        return $label;
    }

    /**
     * Unused method
     */
    public function show() { }


    /**
     * Get the unique name for this object
     *
     * @return string
     */
    public function getId()
    {
        $str = 'fid';
        if ($this->getForm()) {
            $str = $this->getForm()->getId();
        }
        return $str . '_' . $this->name;
    }

    /**
     * Get the unique name for this object
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name for this object
     *
     * @param $name
     * @return Element
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the label of this element
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the label of this element
     *
     * @param $str
     * @return Element
     */
    public function setLabel($str)
    {
        $this->label = $str;
        return $this;
    }

    /**
     * Set the enabled state of this field
     *
     * @param bool $b
     * @return $this
     */
    public function setEnabled($b = true)
    {
        $this->enabled = ($b == true);
        return $this;
    }

    /**
     * Is this element enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }


    /**
     * Set the renderable state of this element
     * If hidden the render engins should ignore this element
     * However the Form and EventController objects will still act on this object
     * as it is still enabled.
     *
     * NOTE: This has nothing to do with the hidden field type <input type="hidden" ...
     * @param bool $b
     * @return $this
     */
    public function setHidden($b = true)
    {
        $this->hidden = ($b == true);
        return $this;
    }

    /**
     * Is this element to be rendered
     *
     * NOTE: This has nothing to do with the hidden field type <input type="hidden" ...
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * Set the notes html
     *
     * @param string $html
     * @return Element
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
     * Add an event/attribute to the element
     * EG: onChange, onClick, etc
     * Allthough better to use Js selectors outside of attributes.
     * EG: jQuery: $('#fid-field').click(.....);
     *
     * @param $attrName
     * @param $value
     * @return Element
     */
    public function addAttr($attrName, $value)
    {
        $this->attrList[$attrName] = $value;
        return $this;
    }

    /**
     * Remove an attribute that has been added via addAttr()
     * @param $attrName
     * @return Element
     */
    public function removeAttr($attrName)
    {
        if (isset($this->attrList[$attrName])) {
            unset($this->attrList[$attrName]);
        }
        return $this;
    }

    /**
     * get the attribute list for this element
     *
     * @return array
     */
    public function getAttrList()
    {
        return $this->attrList;
    }

    /**
     * Clear the atributes list
     * @return Element
     */
    public function clearAttrList()
    {
        $this->attrList = array();
        return $this;
    }


    /**
     * Get the form CSS class
     *
     * @param $className
     * @return $this
     */
    public function addCssClass($className)
    {
        $this->cssList[$className] = $className;
        return $this;
    }

    /**
     *
     * @param $className
     * @return v
     */
    public function deleteCssClass($className)
    {
        unset($this->cssList[$className]);
        return $this;
    }

    /**
     * Clear the style class list
     *
     * @return Element
     */
    public function clearCssClassList()
    {
        $this->cssList = array();
        return $this;
    }

    /**
     * Get the class style list for this element
     *
     * @return array
     */
    public function getCssClassList()
    {
        if (!is_array($this->cssList))
            $this->clearCssClassList();
        return $this->cssList;
    }


    /**
     * Add a style to the form element
     *
     * @param string $style
     * @param string $value
     * @return Element
     */
    public function addStyle($style, $value)
    {
        $this->styleList[$style] = $value;
        return $this;
    }

    /**
     * Remove a style element
     *
     * @param $style
     * @return Element
     */
    public function deleteStyle($style)
    {
        if (isset($this->styleList[$style])) {
            unset($this->styleList[$style]);
        }
        return $this;
    }

    /**
     * Clear the style list
     * @return Element
     */
    public function clearStyleList()
    {
        $this->styleList = array();
        return $this;
    }

    /**
     * Get the style list for this element
     *
     * @return array
     */
    public function getStyleList()
    {
        return $this->styleList;
    }

    /**
     * Add an error message html to the element
     *
     * @param string|array $msg
     * @return Element
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
     * Get the error as a formatted html string
     *
     * @return string
     */
    public function getErrrorHtml()
    {
        $html = '';
        foreach ($this->errors as $i => $msg) {
            $html .= $msg;
            if ($i < count($this->errors)-1) {
                $html .= "<br/>\n";
            }
        }
        return $html;
    }

    /**
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Does this element contain errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        if (count($this->getErrors())) {
            return true;
        }
        return false;
    }

    /**
     * Set the form for this element
     *
     * @param Form $form
     * @return Element
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * Get the toplevel form
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

}
