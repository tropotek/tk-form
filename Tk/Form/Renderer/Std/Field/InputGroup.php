<?php

namespace Tk\Form\Renderer\Std\Field;

use Tk\Form\Exception;
use Tk\Form\Renderer\Std\FieldRendererInterface;

class InputGroup extends FieldRendererInterface
{

    /**
     * @param array<string,mixed> $data
     */
    function show(array $data = []): string
    {
        $field = $this->getField();
        if (!($field instanceof \Tk\Form\Field\InputGroup)) {
            throw new Exception("Invalid field renderer selected");
        }

        if (!(is_array($field->getValue()) || is_object($field->getValue()))) {
            $field->setAttr('value', $field->getValue());
        }

        $data = $this->decorate($data);

        if ($field->getPreText()) {
            $data['preBlock']['pre'] = $field->getPreText();
        }
        if ($field->getPostText()) {
            $data['postBlock']['post'] = $field->getPostText();
        }

        $data['inputGroupCss'] = '';
        if ($field->hasError()) {
            $data['inputGroupCss'] = $field->getParam('error-css');
        }

        return $this->getTemplate()->parse($data);
    }

}