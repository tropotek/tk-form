<?php
namespace Tk\Form\Renderer\Std\Action;

use Tk\Form\Exception;

class SubmitExit extends Submit
{

    function show(array $data = []): string
    {
        $data['name'] = $this->getField()->getId();

        $field = $this->getField();
        if (!($field instanceof \Tk\Form\Action\SubmitExit)) {
            throw new Exception("Invalid field renderer selected");
        }

        if (is_string($field->getValue())) {
            $data['value'] = $field->getValue() . '-exit';
            $data['title'] = ucfirst($field->getValue()) . ' and exit';
            if ($field->isDisabled()) {
                $data['disabled'] = 'disabled';
            }
        }

        return parent::show($data);
    }
}