<?php

namespace Tk\Form\Renderer\Std;

use Tk\CurlyTemplate;
use Tk\Form\Element;
use Tk\Form\Field\FieldInterface;

abstract class FieldRendererInterface
{

    /**
     * Native form field namespace
     */
    const FIELD_NS_LIST = [
        'Tk\\Form\\Field',
        'Tk\\Form\\Action'
    ];

    protected Element|null $field = null;

    protected Renderer|null $formRenderer = null;

    protected CurlyTemplate|null $template = null;


    public function __construct(Element $field, Renderer $formRenderer)
    {
        $this->field = $field;
        $this->formRenderer = $formRenderer;
    }

    abstract function show(array $data = []): string;

    protected function decorate(array $data = []): array
    {
        /** @var FieldInterface $field */
        $field = $this->getField();

        if ($field->getNotes()) {
            $data['noteBlock'][] = [
                'notes' => $field->getNotes(),
            ];
        }
        if ($field->hasError()) {
            if ($this->getFormRenderer()->getParam('error-css')) {
                $field->getFieldCss()->addCss($this->getFormRenderer()->getParam('error-css'));
                $field->addCss($this->getFormRenderer()->getParam('error-css'));
                //$data['fieldCss'] = $this->getFormRenderer()->getParam('error-css') . ' ';
                //$data['fieldAttrs'] = $this->getFormRenderer()->getParam('error-css') . ' ';
            }
            $data['errorBlock'][] = [
                'error' => $field->getError()
            ];
        }

        $field->getOnShow()->execute($this, $data);

        // Add any attributes
        $data['fieldCss'] = $field->getFieldCss()->getCssString();
        $data['fieldAttrs'] = $field->getFieldAttr()->getAttrString();
        $data['css'] = $field->getCssString();
        $data['attrs'] = $field->getAttrString();

        // Render Label
        if($field->getLabel()) {
            $data['labelBlock'][] = [
                'labelAttrs' => 'for="'.$field->getId().'" ',
                'labelCss' => '',
                'label' => $field->getLabel(),
            ];
        }
        return $data;
    }

    public function getField(): ?Element
    {
        return $this->field;
    }

    public function getFormRenderer(): ?Renderer
    {
        return $this->formRenderer;
    }

    public function getTemplate(): ?CurlyTemplate
    {
        return $this->template;
    }

    public function setTemplate(?CurlyTemplate $template): FieldRendererInterface
    {
        $this->template = $template;
        return $this;
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
            $rendererClass = 'Tk\Form\Renderer\Std\Field\Input';
        }
        //vd($field::class, $fieldNS, $fieldClass, $rendererClass);

        return new $rendererClass($field, $formRenderer);

    }
}