<?php
namespace Tk\Form\Field;

use Tk\Form\Exception;
use Tk\Form;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
abstract class Iface extends \Tk\Form\Element implements \Dom\Renderer\RendererInterface
{

    /**
     * An array of all field and sub-field string for this field
     * @var array
     */
    protected $values = array();
    
    /**
     * @var bool
     */
    protected $required = false;
    
    /**
     * This will be true if the element is an array IE: name="title[]"
     * the "[]" will be removed from the name
     * @var bool
     */
    protected $arrayField = false;
        
    /**
     * @var string
     */
    protected $fieldset = '';

    /**
     * @var string
     */
    protected $tabGroup = '';

    /**
     * @var mixed
     */
    protected $template = null;
    
    

    /**
     * __construct
     *
     * @param string $name
     * @throws Exception
     */
    public function __construct($name)
    {
        $this->setName($name);
    }
     

    /**
     * Set the name for this element
     *
     * When using the element with an array name (EG: 'name[]')
     * The '[]' are removed from the name but the isArray value is set to true.
     *
     * NOTE: only single dimensional numbered arrays are supported,
     *  Multidimensional or named arrays are not.
     *  Invalid field name examples are:
     *   o 'name[key]'
     *   o 'name[][]'
     *   o 'name[key][]'
     *
     * @param $name
     * @return $this
     * @throws Exception
     */
    public function setName($name)
    {
        $n = $name;
        if (substr($n, -2) == '[]') {
            $this->arrayField = true;
            $n = substr($n, 0, -2);
        }
        if (strstr($n, '[') !== false) {
            throw new Exception('Invalid field name: ' . $n);
        }
        parent::setName($n);
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldset()
    {
        return $this->fieldset;
    }

    /**
     * @param string $fieldset
     * @return $this
     */
    public function setFieldset($fieldset)
    {
        $this->fieldset = $fieldset;
        return $this;
    }

    /**
     * @return string
     */
    public function getTabGroup()
    {
        return $this->tabGroup;
    }

    /**
     * @param string $tabGroup
     * @return $this
     */
    public function setTabGroup($tabGroup)
    {
        $this->tabGroup = $tabGroup;
        return $this;
    }


    /**
     * Set the field value(s)
     *
     * @param array|string $values
     * @return $this
     */
    public function setValue($values)
    {
        if (!is_array($values)) {
            $values = array($this->getName() => $values);
        }
        if (!isset($values[$this->getName()])) return $this;
   
        $this->values[$this->getName()] = $values[$this->getName()];

        return $this;
    }

    /**
     * Get the field value(s).
     * 
     * @return string|array
     */
    public function getValue()
    {
        if (isset($this->values[$this->getName()])) {
            return $this->values[$this->getName()];
        }
        return '';
    }

    /**
     * @return array
     */
    public function getValueArray() 
    {
        return $this->values;
    }

    /**
     * isRequired
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * setRequired
     *
     * @param boolean $required
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * Does this fields data come as an array.
     * If the name ends in [] then it will be flagged as an arrayField.
     *
     * EG: name=`name[]`
     *
     * @return boolean
     */
    public function isArray()
    {
        return $this->arrayField;
    }

    /**
     * Set to true if this element is an array set
     * 
     * EG: name=`name[]`
     * 
     * @param $b
     * @return $this
     */
    public function setArrayField($b)
    {
        $this->arrayField = $b;
        return $this;
    }



    /* \Dom\Renderer\RendererInterface */
    
    
    /**
     * Set a new template for this renderer.
     *
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Get the template
     * This method will try to call the magic method __makeTemplate
     * to create a template if non exits.
     *
     * @return mixed
     */
    public function getTemplate()
    {
        // Not sure if this is the correct spot for this, but it need to be called by all fields after the form is set.
        if (!$this->getAttr('id')) {
            $this->setAttr('id', $this->makeId());
        }

        $magic = '__makeTemplate';
        if (!$this->hasTemplate() && method_exists($this, $magic)) {
            $this->template = $this->$magic();
        }
        return $this->template;
    }

    /**
     * Test if this renderer has a template and is not NULL
     *
     * @return bool
     */
    public function hasTemplate()
    {
        if ($this->template) {
            return true;
        }
        return false;
    }
    
}