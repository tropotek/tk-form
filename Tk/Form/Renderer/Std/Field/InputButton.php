<?php

namespace Tk\Form\Renderer\Std\Field;

use Tk\Form\Exception;
use Tk\Form\Field\FieldInterface;
use Tk\Form\Renderer\Std\FieldRendererInterface;

class InputButton extends FieldRendererInterface
{

    function show(array $data = []): string
    {
        $field = $this->getField();
        if (!($field instanceof \Tk\Form\Field\InputButton)) {
            throw new Exception("Invalid field renderer selected");
        }

        $data = $this->decorate($data);

        $data['buttonCss'] = $field->getBtnAttr()->getCssString();
        $data['buttonAttrs'] = $field->getBtnAttr()->getAttrString();

        $data['buttonText'] = '';
        if ($field->getBtnText()) {
            $data['buttonText'] = $field->getBtnText();
        }

        $data['inputGroupCss'] = '';
        if ($field->hasError()) {
            $data['inputGroupCss'] = $this->getFormRenderer()->getParam('error-css');
        }

        return $this->getTemplate()->parse($data);
    }

}