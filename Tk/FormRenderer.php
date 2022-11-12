<?php
namespace Tk;

use Dom\Builder;
use Dom\Renderer\Renderer;
use Dom\Renderer\RendererInterface;
use Dom\Repeat;
use Dom\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tk\Form\Event\FormEvent;
use Tk\Form;
use Tk\Traits\SystemTrait;

/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class FormRenderer extends Renderer
{
    use SystemTrait;

    /**
     * Constants for field render tree
     */
    const GROUP       = '__group';
    const FIELDSET    = '__fieldset';
    const FIELD       = '__field';

    protected Form $form;

    protected Builder $builder;

    protected array $groupTemplates = [];

    protected array $fieldsetTemplates = [];

    protected array $params = [];



    public function __construct(Form $form, string $tplFile)
    {
        $this->form = $form;
        $this->builder = new Builder($tplFile);
        $this->init($tplFile);
    }

    protected function init(string $tplFile)
    {
        // Setup default options.
        // These can be set in the form attributes data...
        $this->params = [
            'error-css' => 'is-invalid',
            'valid-css' => 'is-valid',
        ];

        // get any data-opt options from the template and remove them
        $formEl = $this->builder->getDocument()->getElementById('tpl-form');
        $cssPre = 'data-opt-';
        /** @var \DOMAttr $attr */
        foreach ($formEl->attributes as $attr) {
            if (str_starts_with($attr->name, $cssPre)) {
                $name = str_replace($cssPre, '', $attr->name);
                $this->params[$name] = $attr->value;
            }
        }
        // Remove option attributes
        foreach ($this->params as $k => $v) {
            $formEl->removeAttribute($cssPre . $k);
        }

        $this->setTemplate($this->builder->getTemplate('tpl-form'));
        /** @var Form\Field\FieldInterface $field */
        foreach ($this->getForm()->getFields() as $field) {
            if ($field->hasTemplate()) continue;
            $field->setTemplate($this->buildFieldTemplate($field->getType()));
        }
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
        $tpl = $this->builder->getTemplate('tpl-form-' . $type);
        return $tpl;
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


    function show(): ?Template
    {
        if (!$this->hasTemplate()) throw new \Tk\Form\Exception('Form template not found!');
        $template = $this->getTemplate();

        $e = new FormEvent($this->getForm());
        $this->getForm()->getDispatcher()?->dispatch($e, Form\FormEvents::FORM_SHOW_PRE);

        // Show all fields
        $this->showFields($template);

        // Set form attrs
        $template->setAttr('form' ,$this->getForm()->getAttrList());
        $template->addCss('form', $this->getForm()->getCssList());

        $this->getForm()->getDispatcher()?->dispatch($e, Form\FormEvents::FORM_SHOW);
        return $template;
    }

    /**
     * Render Fields
     */
    protected function showFields(Template $template)
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
                    if ($field instanceof Form\Field\Hidden) {
                        $template->prependTemplate('form',  $field->show());
                    } else if ($field instanceof Form\Action\ActionInterface) {
                        $template->appendTemplate('actions', $field->show());
                    } else {
                        $template->appendTemplate('fields', $field->show());
                    }
                }
            }
        }
    }

    protected function showGroup(array $fields, string $group): Template
    {
        if (!$this->hasGroupTemplate($group)) {
            $template = $this->getGroupTemplate($group);
            if ($template != $this->getTemplate()) {
                $id = strtolower(preg_replace('/[^a-z0-9]/i', '-', $group));
                $template->setAttr('fields', 'id', $this->getForm()->makeInstanceKey('grp-' . $id));
                $template->setAttr('fields', 'data-name', $id);
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
                $template->appendTemplate('fields', $children->show());
            }
        }

        return $template;
    }

    protected function showFieldset(array $fields, string $fieldset, string $group = ''): Template
    {
        if (!$this->hasFieldsetTemplate($fieldset, $group)) {
            $template = $this->getFieldsetTemplate($fieldset, $group);
            if ($template != $this->getTemplate()) {
                $id = strtolower(preg_replace('/[^a-z0-9]/i', '-', $fieldset));
                $template->setAttr('fields', 'id', $this->getForm()->makeInstanceKey('fs-' . $id));
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
            $template->appendTemplate('fields', $field->show());
        }

        return $template;
    }


    /**
     * Sort all fields into their groups and fieldsets
     */
    protected function getRenderTree($fieldList): array
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
        //vd(Collection::arrayToString($sets));
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