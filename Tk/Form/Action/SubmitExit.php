<?php
namespace Tk\Form\Action;

use Dom\Template;

class SubmitExit extends Submit
{

    public function __construct(string $name, callable $callback = null)
    {
        parent::__construct($name, $callback);
        $this->setType('submit-exit');
    }

    function show(): ?Template
    {
        $template = parent::show();

        // Render Element
        $template->setAttr('exit', 'name', $this->getId());
        $template->setAttr('exit', 'value', $this->getValue().'-exit');
        $template->setAttr('exit', 'title', ucfirst($this->getValue()) . ' and exit');

        return $template;
    }
}