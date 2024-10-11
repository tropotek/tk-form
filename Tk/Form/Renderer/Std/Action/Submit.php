<?php
namespace Tk\Form\Renderer\Std\Action;

use Tk\Form\Field\FieldInterface;
use Tk\Form\Renderer\Std\FieldRendererInterface;

class Submit extends FieldRendererInterface
{

    function show(array $data = []): string
    {
        /** @var \Tk\Form\Action\Submit $field */
        $field = $this->getField();

        // Render Element
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

        if ($field instanceof \Tk\Form\Action\Submit) {
            $field->getOnShow()->execute($field);
        }

        $data = $this->decorate($data);
        $data['text'] = $field->getLabel();

        return $this->getTemplate()->parse($data);
    }

}