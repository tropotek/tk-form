<?php
namespace Tk\Form\Field;


/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class Textarea extends Input
{

    public function __construct(string $name)
    {
        parent::__construct($name, self::TYPE_TEXTAREA);
    }

}