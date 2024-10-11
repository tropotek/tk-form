<?php
namespace Tk\Form\Renderer\Dom;

use Dom\Builder;
use Dom\Repeat;
use Dom\Template;
use Tk\Exception;
use Tk\Form\Event\FormEvent;
use Tk\Form\Field\FieldInterface;
use Tk\Form;

/**
 * Use the DOM template to render a form
 */
class Renderer extends \Dom\Renderer\Renderer
{

    /**
     * Constants for field render tree
     */
    const GROUP       = '__group';
    const FIELDSET    = '__fieldset';
    const FIELD       = '__field';

    protected array   $groupTemplates    = [];
    protected array   $fieldsetTemplates = [];
    protected array   $params            = [];
    protected Form    $form;
    protected Builder $builder;


    public function __construct(Form $form, string $tplFile = null)
    {
        $this->form = $form;
        if (!$tplFile) {
            $tplFile = dirname(__DIR__, 4) . '/templates/bs5_dom.html';
        }
        if (!is_file($tplFile)) {
            throw new Exception('Cannot read file: ' . $tplFile);
        }
        $this->builder = new Builder($tplFile);

        $this->init();
    }

    public static function createInlineRenderer(Form $form, string $tplFile = null): self
    {
        if (!$tplFile) {
            $tplFile = dirname(__DIR__, 4) . '/templates/bs5_dom_inline.html';
        }
        return new self($form, $tplFile);
    }

    protected function init(): void
    {
        // Setup default options.
        // These can be set in the form attributes data...
        $prefix = 'data-opt-';
        $this->params = [
            'error-css' => 'is-invalid',
            'valid-css' => 'is-valid',
        ];

        // get any data-opt options from the template and remove them
        $formEl = $this->builder->getDocument()->getElementById('tpl-form');
        /** @var \DOMAttr $attr */
        foreach ($formEl->attributes as $attr) {
            if (str_starts_with($attr->name, $prefix)) {
                $name = str_replace($prefix, '', $attr->name);
                $this->params[$name] = $attr->value;
            }
        }
        // Remove option attributes
        foreach ($this->params as $k => $v) {
            $formEl->removeAttribute($prefix . $k);
        }

        $this->setTemplate($this->builder->getTemplate('tpl-form'));
    }

    public function buildFieldTemplate(string $type): Template
    {
        $tpl = $this->builder->getTemplate('tpl-form-' . $type);
        if (!$tpl) {
            $tpl = $this->builder->getTemplate('tpl-form-input');
        }
        return $tpl;
    }

    public function buildTemplate(string $type): ?Template
    {
        return $this->builder->getTemplate('tpl-form-' . $type);
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setForm(Form $form): static
    {
        $this->form = $form;
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $name, mixed $default = null): mixed
    {
        return $this->params[$name] ?? $default;
    }

    /**
     * Add CSS to every field group element in the form
     */
    public function addFieldCss(string $css): static
    {
        foreach ($this->getForm()->getFields() as $field) {
            $field->getFieldCss()->addCss($css);
        }
        return $this;
    }

    /**
     * Remove CSS from every field group element in the form
     */
    public function removeFieldCss(string $css): static
    {
        foreach ($this->getForm()->getFields() as $field) {
            $field->getFieldCss()->removeCss($css);
        }
        return $this;
    }


    function show(): ?Template
    {
        if (!$this->hasTemplate()) throw new \Tk\Form\Exception('Form template not found!');
        $template = $this->getTemplate();

//        $e = new FormEvent($this->getForm());
//        $this->getForm()->getDispatcher()?->dispatch($e, Form\FormEvents::FORM_SHOW_PRE);

        // Show all fields
        $this->showFields($template);

        // Set form attrs
        $list = $this->getForm()->getAttrList();
        //unset($list['name']); // remove name from the attrs as the form does not need it
        $template->setAttr('form', $list);
        $template->addCss('form', $this->getForm()->getCssList());

        // Show form errors
        foreach ($this->getForm()->getErrors() as $error) {
            $r = $template->getRepeat('error');
            $r->setHtml('error', $error);
            $r->appendRepeat();
            $template->setVisible('errors');
        }

//        $this->getForm()->getDispatcher()?->dispatch($e, Form\FormEvents::FORM_SHOW);
        return $template;
    }

    /**
     * Render Fields
     */
    protected function showFields(Template $template): void
    {
        // Build a render tree with the groups, fieldsets in the correct order
        $fields = $this->getRenderTree($this->getForm()->getFields());

        /* @var $children Form\Field\FieldInterface|Form\Field\FieldInterface[] */
        foreach ($fields as $id => $children) {
            if ($id == self::GROUP) {
                foreach ($children as $group => $grpFields) {
                    $tpl = $this->showGroup($grpFields, $group);
                    if ($tpl instanceof Repeat) {
                        $tpl->appendRepeat();
                    } else if ($tpl !== $this->getTemplate()) {
                        $template->appendTemplate('fields', $tpl);
                    }
                }
            } else if ($id == self::FIELDSET) {
                foreach ($children as $fieldset => $fsFields) {
                    $tpl = $this->showFieldset($fsFields, $fieldset);
                    if ($tpl instanceof Repeat) {
                        $tpl->appendRepeat();
                    } else if ($tpl !== $this->getTemplate()) {
                        $template->appendTemplate('fields', $tpl);
                    }
                }
            } else {
                foreach ($children as $field) {
                    $tpl = $this->showField($field);
                    if (!$tpl) continue;

                    if ($field instanceof Form\Field\Hidden) {
                        $template->prependTemplate('form',  $tpl);
                    } else if ($field instanceof Form\Action\ActionInterface) {
                        $template->appendTemplate('actions', $tpl);
                    } else {
                        $template->appendTemplate('fields', $tpl);
                    }
                }
            }
        }
    }

    /**
     * Render Fields
     */
    protected function showField(FieldInterface $field): ?Template
    {
        $template = $this->buildFieldTemplate($field->getType());
        //$field->replaceParams($this->getParams());      // TODO: do we need this, would be good to deprecate field params

        $renderer = FieldRendererInterface::createRenderer($field, $this);
        $renderer->setTemplate($template);
        return $renderer->show();
    }

    protected function showGroup(array $fields, string $group): Template
    {
        if (!$this->hasGroupTemplate($group)) {
            $template = $this->getGroupTemplate($group);
            if ($template !== $this->getTemplate()) {
                $id = strtolower(preg_replace('/[^a-z0-9]/i', '-', $group));
                $template->setAttr('fields', 'id', $this->getForm()->makeRequestKey('grp-' . $id));
                $template->setAttr('fields', 'data-name', $group);
                $template->addCss('fields', 'grp-' . $id);
            }
        }
        $template = $this->getGroupTemplate($group);

        /* @var $children Form\Field\FieldInterface|array */
        foreach ($fields as $key => $children) {
            if ($key == self::FIELDSET) {
                foreach ($children as $fieldset => $fsChildren) {
                    $tpl = $this->showFieldset($fsChildren, $fieldset, $group);
                    if ($tpl instanceof Repeat) {
                        $tpl->appendRepeat();
                    } else if ($template !== $this->getTemplate()) {
                        $template->appendTemplate('fields', $tpl);
                    }
                }
            } else {
                $ftpl = $this->showField($children);
                if ($ftpl) $template->appendTemplate('fields', $ftpl);
            }
        }

        return $template;
    }

    protected function showFieldset(array $fields, string $fieldset, string $group = ''): Template
    {
        if (!$this->hasFieldsetTemplate($fieldset, $group)) {
            $template = $this->getFieldsetTemplate($fieldset, $group);
            if ($template !== $this->getTemplate()) {
                $id = strtolower(preg_replace('/[^a-z0-9]/i', '-', $fieldset));
                $template->setAttr('fields', 'id', $this->getForm()->makeRequestKey('fs-' . $id));
                $template->setAttr('fields', 'data-name', $id);
                $template->addCss('fields', 'fs-' . $id);
            }
        }
        $template = $this->getFieldsetTemplate($fieldset, $group);

        if ($template->hasVar('legend')) {
            $template->setText('legend', $fieldset);
        }

        /** @var Form\Field\FieldInterface $field */
        foreach ($fields as $field) {
            $template->setAttr('fields', $field->getFieldsetAttr()->getAttrList());
            $ftpl = $this->showField($field);
            if ($ftpl) $template->appendTemplate('fields', $ftpl);
            //$template->appendTemplate('fields', $field->show());
        }

        return $template;
    }


    /**
     * Sort all fields into their groups and fieldsets
     */
    protected function getRenderTree(array $fieldList): array
    {
        $sets = [
            self::GROUP => [],
            self::FIELDSET => [],
            self::FIELD => [],
        ];
        /* @var $field Form\Field\FieldInterface */
        foreach ($fieldList as $field) {
            if ($field instanceof Form\Field\Hidden) {
                $sets[self::FIELD][] = $field;
                continue;
            }
            if ($field->getGroup()) {
                if ($field->getFieldset()) {
                    $sets[self::GROUP][$field->getGroup()][self::FIELDSET][$field->getFieldset()][] = $field;
                } else {
                    $sets[self::GROUP][$field->getGroup()][] = $field;
                }
            } else {
                if ($field->getFieldset()) {
                    $sets[self::FIELDSET][$field->getFieldset()][] = $field;
                } else {
                    $sets[self::FIELD][] = $field;
                }
            }
        }
        return $sets;
    }

    public function hasGroupTemplate(string $group): bool
    {
        return isset($this->groupTemplates[$group]);
    }

    public function getGroupTemplate(string $group): Template
    {
        if (!$this->hasGroupTemplate($group)) {
            $this->groupTemplates[$group] = $this->getTemplate()->getRepeat('group-' . $group);
            if (!$this->hasGroupTemplate($group)) {
                $this->groupTemplates[$group] = $this->buildTemplate('group-' . $group);
            }
            if (!$this->hasGroupTemplate($group)) {
                $this->groupTemplates[$group] = $this->buildTemplate('group');
            }
            if (!$this->hasGroupTemplate($group)) return $this->getTemplate();
        }
        return $this->groupTemplates[$group];
    }

    public function hasFieldsetTemplate(string $fieldset, string $group = ''): bool
    {
        return isset($this->fieldsetTemplates[$group][$fieldset]);
    }

    public function getFieldsetTemplate(string $fieldset, string $group = ''): Template
    {
        if (!$this->hasFieldsetTemplate($fieldset, $group)) {
            $this->groupTemplates[$group] = $this->getTemplate()->getRepeat('fieldset-' . $fieldset);
            if (!$this->hasFieldsetTemplate($fieldset, $group)) {
                $this->fieldsetTemplates[$group][$fieldset] = $this->buildTemplate('fieldset-' . $fieldset);
            }
            if (!$this->hasFieldsetTemplate($fieldset, $group)) {
                $this->fieldsetTemplates[$group][$fieldset] = $this->buildTemplate('fieldset');
            }
            if (!$this->hasFieldsetTemplate($fieldset, $group)) return $this->getTemplate();
        }
        return $this->fieldsetTemplates[$group][$fieldset];
    }
}