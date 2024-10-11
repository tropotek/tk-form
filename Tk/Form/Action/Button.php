<?php
namespace Tk\Form\Action;

class Button extends Submit
{

    public function __construct(string $name, callable $callback = null)
    {
        parent::__construct($name, $callback);
        $this->setType(self::TYPE_BUTTON);
    }

}