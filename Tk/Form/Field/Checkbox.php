<?php
namespace Tk\Form\Field;

use Tk\Form\Type;

/**
 * Class Text
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Checkbox extends Iface
{


    /**
     * Is the value checked
     *
     * @return bool
     */
    public function isSelected($val = '')
    {
        $arr = $this->getType()->getTextValue();
        if (!empty($arr[$this->name]) && $arr[$this->name] == $this->name) {
            return true;
        }
        return false;
    }


}