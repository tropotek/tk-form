<?php
namespace Tk\Form\Field;


/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class Input extends FieldInterface
{

    public function getValue(): mixed
    {
        if (is_string(parent::getValue()))
            $this->setValue(trim(parent::getValue()));
        return parent::getValue();
    }

}