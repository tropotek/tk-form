<?php
namespace Tk\Form\Field;


/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Hidden extends Input
{


    /**
     * __construct
     *
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value = '')
    {
        parent::__construct($name);
        $this->setValue($value);
        $this->setType('hidden');
    }

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        $t = parent::getHtml();
        
        return $t;
    }

}