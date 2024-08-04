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
    const FIELD_NS_LIST = [
        'Tk\\Form\\Field',
        'Tk\\Form\\Action'
    ];

    protected Element|null $field = null;

    protected Renderer|null $formRenderer = null;


    public function __construct(Element $field, Renderer $formRenderer)
    {
        $this->field = $field;
        $this->formRenderer = $formRenderer;
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
            if ($this->getFormRenderer()->getParam('error-css')) {
                $field->addCss($this->getFormRenderer()->getParam('error-css'));
                $template->addCss('is-error', $this->getFormRenderer()->getParam('error-css'));
            }
            $template->setHtml('error', $field->getError());
            $template->setVisible('error');
        }

        $field->getOnShow()->execute($this, $template);

        // Add any attributes
        $template->addCss('field', $field->getFieldCss()->getCssList());
        $template->setAttr('field', $field->getFieldAttr()->getAttrList());
        $template->setAttr('element', $field->getAttrList());
        $template->addCss('element', $field->getCssList());

        // Render Label
        if($field->getLabel()) {
            $template->setText('label', $field->getLabel());
            $template->setAttr('label', 'for', $field->getId());
            $template->setVisible('label');
        }

    }

    public function getField(): ?Element
    {
        return $this->field;
    }

    public function getFormRenderer(): ?Renderer
    {
        return $this->formRenderer;
    }

    public static function createRenderer(FieldInterface $field, Renderer $formRenderer): ?FieldRendererInterface
    {
        // field class and namespace
        [$fieldNS, $fieldClass] = str_split($field::class, strrpos($field::class, '\\')+1);
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
        return new $rendererClass($field, $formRenderer);

    }
}