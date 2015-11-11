<?php
namespace Tk\Form\Field;

use Tk\Form\Exception;
use \Tk\Form;
use Tk\Form\Type;

/**
 * Class Iface
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
abstract class Iface extends \Tk\Form\Element
{

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var string
     */
    protected $notes = '';

    /**
     * @var bool
     */
    protected $arrayField = false;

    /**
     * @var Type\Iface
     */
    protected $type = null;

    /**
     * @var mixed
     */
    protected $renderer = null;


    /**
     * __construct
     *
     * @param string $name
     * @param Type\Iface|null $type
     * @throws Exception
     */
    public function __construct($name, Type\Iface $type = null)
    {
        $this->setName($name);
        if (!$type) {
            $type = new Type\String();
        }
        $this->setType($type);
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
        $label = ucfirst(preg_replace('/[A-Z]/', ' $0', $name));
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
     * @return Type\Iface
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Type\Iface $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        $type->setField($this);
        return $this;
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
        $this->name = $n;

        if (!$this->getLabel()) {
            $this->setLabel(self::makeLabel($this->getName()));
        }
        if (!$this->getAttr('id')) {
            $this->setAttr('id', $this->makeId());
        }

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
     * Get the renderer object
     *
     * @return mixed
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Set the renderer object
     *
     * @param mixed $renderer
     * @return $this
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
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
     * Set the complex type value of the field,
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->getType()->setValue($value);
        return $this;
    }

    /**
     * Get the field value based on its type.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->getType()->getValue();
    }

}