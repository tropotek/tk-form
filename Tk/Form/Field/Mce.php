<?php
namespace Tk\Form\Field;


class Mce extends Textarea
{

    public function __construct(string $name, string $style = 'mce')
    {
        parent::__construct($name);
        $this->setType('mce');
        $this->addCss($style);
    }

}