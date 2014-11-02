<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 *  A form text field object
 *
 * @note HTML5 element
 * @package Form\Field
 */
class Range extends Iface
{

    protected $min = -10;

    protected $max = 10;

    protected $step = 1;


    /**
     * __construct
     *
     * @param string $name
     * @param int $min
     * @param int $max
     * @param int $step
     * @param \Form\Type\Iface $type
     */
    public function __construct($name, $min = -10, $max = 10, $step = 1, $type = null)
    {
        $this->min = (int)$min;
        $this->max = (int)$max;
        $this->step = (int)$step;
        
        $this->setName($name);
        $this->setLabel(self::makeLabel($name));
        $this->setType($type);
        if (!$this->getType()) {
            $this->setType(new \Form\Type\Integer());
        }
    }

    /**
     * show
     */
    public function show()
    {
        parent::show();
        $t = $this->getTemplate();

        $t->setAttr('element', 'type', 'range');
        $t->setAttr('element', 'min', $this->min);
        $t->setAttr('element', 'max', $this->max);
        $t->setAttr('element', 'step', $this->step);

    }
}