<?php
namespace Tk\Form\Field;


/**
 *
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Password extends Input
{


    /**
     * __construct
     *
     * @param string $name
     * @throws \Tk\Form\Exception
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setType('password');
        $this->setAttr('autocomplete', 'off');
    }


}