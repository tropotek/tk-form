<?php
namespace Tk\Form\Action;

class SubmitExit extends Submit
{

    public function __construct(string $name, callable $callback = null)
    {
        parent::__construct($name, $callback);
        $this->setType('submit-exit');
    }

}