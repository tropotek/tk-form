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
     * @var array|null
     * @deprecated
     */
    //protected $values = array();
    protected $values = null;




    /**
     * @var mixed|null
     */
    protected $value = null;
    
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
     * Get the unique name for this element
     *
     * @return string
     */
    public function getFieldName()
    {
        $n = $this->getName();
        if ($this->isArrayField()) {
            $n .= '[]';
        }
        return $n;
    }


    /**
     * Assumes the field value resides within an array
     * EG:
     *   array(
     *    'fieldName1' => 'value1',
     *    'fieldName2' => 'value2',
     *    'fieldName3[]' => array('value3.1', 'value3.2', 'value3.3', 'value3.4'),  // same as below
     *    'fieldName3' => array('value3.1', 'value3.2', 'value3.3', 'value3.4')     // same
     * );
     *
     * @param array|\ArrayObject $values
     * @return $this
     */
    public function load($values)
    {
        // If an array is passed in, and a value is modified
        //  The value is not modified on its instance only its copy here. (Thus the need for a reference)
        // The other issue is that we cannot do calls like setValue('test');

        // 1. One solution is to use a collection object for the values array
        //    or create our own array object for forms.
        // 2. Another solution could be to create a new method, setValuesArray(&$values)
        //    in conjunction with the setValue($value)

        // TODO I have removed the reference from the $value, find out what caused us to use it in the first place....???

        // If an array and the submitted value is not in a proper value array format
        //if ($this->isArray() && !isset($values[$this->getName()])) {
//        if ($this->isArrayField() && !$this->isAssoc($values)) {
//            $values = array($this->getName() => $values);
//        }
//        if (!is_array($values)) {
//            $values = array($this->getName() => $values);
//        }
        // When the value does not exist it is ignored (may not be the desired result for unselected checkbox or empty select box)
        if (isset($values[$this->getName()])) {
            $this->setValue($values[$this->getName()]);
            $this->values[$this->getName()] = $values[$this->getName()];
        }


        return $this;
    }

    /**
     * Set the field value.
     * Set the exact value the field requires to function.
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

//        // TODO:
//        // If an array is passed in, and a value is modified
//        //  The value is not modified on its instance only its copy here. (Thus the need for a reference)
//        // The other issue is that we cannot do calls like setValue('test');
//
//        // 1. One solution is to use a collection object for the values array
//        //    or create our own array object for forms.
//        // 2. Another solution could be to create a new method, setValuesArray(&$values)
//        //    in conjunction with the setValue($value)
//
//        // TODO I have removed the reference from the $value, find out what caused us to use it in the first place....???
//
//        // If an array and the submitted value is not in a proper value array format
//        //if ($this->isArray() && !isset($values[$this->getName()])) {
//        if ($this->isArray() && !$this->isAssoc($values)) {
//            $values = array($this->getName() => $values);
//        }
//        if (!is_array($values)) {
//            $values = array($this->getName() => $values);
//        }
//
//        // When the value does not exist it is ignored (may not be the desired result for unselected checkbox or empty select box)
//        if (isset($values[$this->getName()])) {
//            $this->values[$this->getName()] = $values[$this->getName()];
//        }

        return $this;
    }

    /**
     * Get the field value(s).
     *
     * @return string|array
     */
    public function getValue()
    {
        return $this->value;
//        if (isset($this->values[$this->getName()])) {
//            return $this->values[$this->getName()];
//        }
//        return '';
    }

    /**
     * @return array
     */
    public function getValueArray()
    {
        return $this->values;
    }

    /**
     * Does this fields data come as an array.
     * If the name ends in [] then it will be flagged as an arrayField.
     *
     * EG: name=`name[]`
     *
     * @return boolean
     */
    public function isArrayField()
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






    /**
     * test if the array is sequential or associative
     *
     * @param $arr
     * @return bool
     */
    protected function isAssoc($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
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