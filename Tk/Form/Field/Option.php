<?php
namespace Tk\Form\Field;

/**
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


    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $value = '';



    /**
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value = '')
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @param string $name
     * @param string $value
     * @return static
     */
    static function create($name, $value = '')
    {
        $obj = new static($name, $value);
        return $obj;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * @return bool
     */
    public function isDisabled()
    {
        return $this->hasAttr('disabled');
    }

    /**
     * @param bool $b
     * @return $this
     */
    public function setDisabled($b = true)
    {
        if ($b) {
            $this->setAttr('disabled', 'disabled');
        } else {
            $this->removeAttr('disabled');
        }
        return $this;
    }

}