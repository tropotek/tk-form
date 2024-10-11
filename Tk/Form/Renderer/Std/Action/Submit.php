<?php
namespace Tk\Form\Renderer\Std\Action;

use Tk\Form\Exception;
use Tk\Form\Field\FieldInterface;
use Tk\Form\Renderer\Std\FieldRendererInterface;

class Submit extends FieldRendererInterface
{

    function show(array $data = []): string
    {
        $field = $this->getField();
        if (!($field instanceof \Tk\Form\Action\Submit)) {
            throw new Exception("Invalid field renderer selected");
        }

        if ($field->getType() != FieldInterface::TYPE_LINK) {
            $field->setAttr('name', $field->getId());
            $field->setAttr('value', $field->getValue());
        }

        $field->setAttr('title', ucfirst($field->getValue()));

        if ($field->getIcon()) {
            if ($field->getIconPosition() == \Tk\Form\Action\Submit::ICON_LEFT) {
                $data['lIconBlock'][] = [
                    'css' => $field->getIcon(),
                ];
            } else {
                $data['rIconBlock'][] = [
                    'css' => $field->getIcon(),
                ];
            }
        }

        $field->getOnShow()->execute($field);

        $data = $this->decorate($data);
        $data['text'] = $field->getLabel();

        return $this->getTemplate()->parse($data);
    }

}