<?php
namespace Tk\Form\Renderer\Std\Action;

class SubmitExit extends Submit
{

    function show(array $data = []): string
    {
        $data['name'] = $this->getField()->getId();
        $data['value'] = $this->getField()->getValue().'-exit';
        $data['title'] = ucfirst($this->getField()->getValue()) . ' and exit';

        return parent::show($data);
    }
}