<?php
namespace Tk\Form\Renderer\Std\Action;

use Tk\Form\Field\FieldInterface;

class SubmitExit extends Submit
{

    function show(array $data = []): string
    {
        $data['name'] = $this->getField()->getId();

        $field = $this->getField();
        if ($field instanceof \Tk\Form\Action\SubmitExit) {
            $data['value'] = $field->getValue() . '-exit';
            $data['title'] = ucfirst($field->getValue()) . ' and exit';
        }

        return parent::show($data);
    }
}