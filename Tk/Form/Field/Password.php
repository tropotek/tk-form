<?php
namespace Tk\Form\Field;

class Password extends Input
{

    public function __construct(string $name)
    {
        parent::__construct($name, self::TYPE_PASSWORD);
    }

}