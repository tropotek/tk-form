<?php
namespace Tk\Form\Renderer;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
class LayoutCol
{
    use \Tk\Dom\AttributesTrait;
    use \Tk\Dom\CssTrait;

    /**
     * @var string
     */
    protected $fieldName = '';

    /**
     * @var bool
     */
    protected $rowEnabled = true;

    /**
     * @var Layout
     */
    protected $layout = null;


    /**
     * @param Layout $layout
     * @param string $fieldName
     */
    public function __construct($layout, $fieldName)
    {
        $this->layout = $layout;
        $this->fieldName = $fieldName;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return bool
     */
    public function isRowEnabled()
    {
        return $this->rowEnabled;
    }

    /**
     * @param bool $b
     */
    public function setRowEnabled($b)
    {
        $this->rowEnabled = $b;
    }

    /**
     * @return Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

}