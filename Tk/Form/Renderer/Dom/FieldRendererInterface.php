<?php

namespace Tk\Form\Renderer\Dom;

use Dom\Renderer\RendererInterface;
use Dom\Renderer\Traits\RendererTrait;
use Tk\Form\Element;
use Tk\Form\Field\FieldInterface;

abstract class FieldRendererInterface implements RendererInterface
{
    use RendererTrait;

    /**
     * Native form field namespace
     */
    const array FIELD_NS_LIST = [
        'Tk\\Form\\Field',
        'Tk\\Form\\Action'
    ];

    protected ?Element  $field = null;


    public function __construct(Element $field)
    {
        $this->field = $field;
    }

    protected function decorate(): void
    {
        $template = $this->getTemplate();
        /** @var FieldInterface $field */
        $field = $this->getField();

        if ($field->getNotes()) {
            $template->setHtml('notes', $field->getNotes());
            $template->setVisible('notes');
        }
        if ($field->hasError()) {
            if ($field->getParam('error-css')) {
                $field->addCss($field->getParam('error-css'));
                $template->addCss('is-error', $field->getParam('error-css'));
            }
            $template->setHtml('error', $field->getError());
            $template->setVisible('error');
        }

        $field->setAttr('name', $field->getHtmlName());

        $field->getOnShow()->execute($field, $template);

        // Add any attributes
        $template->addCss('field', $field->getFieldCss()->getCssList());
        $template->setAttr('field', $field->getFieldAttr()->getAttrList());
        $template->setAttr('element', $field->getAttrList());
        $template->addCss('element', $field->getCssList());

        // Render Label
        if($field->getLabel()) {
            $template->setHtml('label', $field->getLabel());
            $template->setAttr('label', 'for', $field->getId());
            $template->setVisible('label');
        }

    }

    public function getField(): ?Element
    {
        return $this->field;
    }

    public static function createRenderer(FieldInterface $field): ?FieldRendererInterface
    {
        // field class and namespace
        $pos = intval(strrpos($field::class, '\\'));
        if ($pos < 1) {
            return null;
        }
        [$fieldNS, $fieldClass] = str_split($field::class, ($pos+1));
        $fieldNS = rtrim($fieldNS, '\\');
        $subNS = substr($fieldNS, strrpos($fieldNS, '\\')+1);

        // Get the renderer class for the field
        $rendererClass = sprintf('%s\\%s\\%s', __NAMESPACE__, $subNS, $fieldClass);
        if (!in_array($fieldNS, self::FIELD_NS_LIST)) {
            $rendererClass = $field::class . 'Renderer';
        }

        if (!class_exists($rendererClass)) {
            $rendererClass = 'Tk\Form\Renderer\Dom\Field\Input';
        }

        $obj = new $rendererClass($field);
        if ($obj instanceof FieldRendererInterface) return $obj;
        return null;
    }
}