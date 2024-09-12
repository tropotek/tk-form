<?php
namespace Tk\Form\Renderer\Std;

use Tk\CurlyTemplate;
use Tk\Exception;
use Tk\Form\Event\FormEvent;
use Tk\Form\Field\FieldInterface;
use Tk\Form;

/**
 * When creating the renderer be sure to create the instance
 * only after all fields have been added to the form.
 */
class Renderer
{

    /**
     * Constants for field render tree
     */
    const GROUP       = '__group';
    const FIELDSET    = '__fieldset';
    const FIELD       = '__field';

    protected Form $form;

    protected array $formTemplates = [];

    protected array $groupTemplates = [];

    protected array $fieldsetTemplates = [];

    protected array $params = [];


    public function __construct(Form $form, string $tplFile = null)
    {
        $this->form = $form;
        if (!$tplFile) {
            $tplFile = dirname(__DIR__, 4) . '/templates/bs5_std.php';
        }

        if (!is_file($tplFile)) {
            throw new Exception('Cannot read file: ' . $tplFile);
        }
        $this->formTemplates = require($tplFile);

    }

    public static function createInlineRenderer(Form $form, string $tplFile = null): static
    {
        if (!$tplFile) {
            $tplFile = dirname(__DIR__, 4) . '/templates/bs5_std_inline.php';
        }
        return new static($form, $tplFile);
    }


    /**
     * Render the form and return the HTML string
     */
    function show(): string
    {
//        $e = new FormEvent($this->getForm());
//        $this->getForm()->getDispatcher()?->dispatch($e, Form\FormEvents::FORM_SHOW_PRE);

        $this->params += $this->formTemplates['form']['options'] ?? [];

        $this->getForm()->setAttr('novalidate', 'novalidate');

        $ctForm = new CurlyTemplate($this->formTemplates['form']['template'] ?? '');

        $data = [
            'attrs'   => $this->getForm()->getAttrString(),
            'css'     => $this->getForm()->getCssString(),
        ];

        // Show form errors
        foreach ($this->getForm()->getErrors() as $error) {
            $data['errors']['error']['message'] = $error;
        }

        // Show all fields
        $data = $data + $this->showFields();

//        $this->getForm()->getDispatcher()?->dispatch($e, Form\FormEvents::FORM_SHOW);

        $html = $ctForm->parse($data);
        $script = $this->formTemplates['form']['script'] ?? '';

        return sprintf("%s\n<script>\n%s\n</script>\n", $html, $script);
    }

    /**
     * Render Fields
     */
    protected function showFields(): array
    {
        $data = [
            'hidden'  => '',
            'fields'  => '',
            'actions' => '',
        ];

        // Build a render tree with the groups, fieldsets in the correct order
        $fields = $this->getRenderTree($this->getForm()->getFields());

        /* @var $children Form\Field\FieldInterface|Form\Field\FieldInterface[] */
        foreach ($fields as $id => $children) {
//            if ($id == self::GROUP) {
//                foreach ($children as $group => $grpFields) {
//                    $tpl = $this->showGroup($grpFields, $group);
//                    if ($tpl instanceof Repeat) {
//                        $tpl->appendRepeat();
//                    } else if ($tpl !== $this->getTemplate()) {
//                        $template->appendTemplate('fields', $tpl);
//                    }
//                }
//            } else if ($id == self::FIELDSET) {
//                foreach ($children as $fieldset => $fsFields) {
//                    $tpl = $this->showFieldset($fsFields, $fieldset);
//                    if ($tpl instanceof Repeat) {
//                        $tpl->appendRepeat();
//                    } else if ($tpl !== $this->getTemplate()) {
//                        $template->appendTemplate('fields', $tpl);
//                    }
//                }
//            } else {
                foreach ($children as $field) {
                    if ($field instanceof Form\Field\Hidden) {
                        $data['hidden'] .= $this->showField($field);
                    } else if ($field instanceof Form\Action\ActionInterface) {
                        $data['actions'] .= $this->showField($field);
                    } else {
                        $data['fields'] .= $this->showField($field);
                    }
                }
//            }
        }
        return $data;
    }


    public function buildFieldTemplate(string $type): CurlyTemplate
    {
        $tpl = $this->formTemplates['tpl-form-' . $type] ?? $this->formTemplates['tpl-form-input'];
        return new CurlyTemplate($tpl['template']);
    }

    protected function showField(FieldInterface $field): string
    {
        $tpl = $this->buildFieldTemplate($field->getType());

        $renderer = FieldRendererInterface::createRenderer($field, $this);
        $renderer->setTemplate($tpl);

        return $renderer->show();
    }

//    protected function showGroup(array $fields, string $group): string
//    {
//        if (!$this->hasGroupTemplate($group)) {
//            $template = $this->getGroupTemplate($group);
//            if ($template !== $this->getTemplate()) {
//                $id = strtolower(preg_replace('/[^a-z0-9]/i', '-', $group));
//                $template->setAttr('fields', 'id', $this->getForm()->makeInstanceKey('grp-' . $id));
//                $template->setAttr('fields', 'data-name', $group);
//                $template->addCss('fields', 'grp-' . $id);
//            }
//        }
//        $template = $this->getGroupTemplate($group);
//
//        /* @var $children Form\Field\FieldInterface|array */
//        foreach ($fields as $key => $children) {
//            if ($key == self::FIELDSET) {
//                foreach ($children as $fieldset => $fsChildren) {
//                    $tpl = $this->showFieldset($fsChildren, $fieldset, $group);
//                    if ($tpl instanceof Repeat) {
//                        $tpl->appendRepeat();
//                    } else if ($template !== $this->getTemplate()) {
//                        $template->appendTemplate('fields', $tpl);
//                    }
//                }
//            } else {
//                $ftpl = $this->showField($children);
//                if ($ftpl) $template->appendTemplate('fields', $ftpl);
//            }
//        }
//
//        return $template;
//    }

//    protected function showFieldset(array $fields, string $fieldset, string $group = ''): Template
//    {
//        if (!$this->hasFieldsetTemplate($fieldset, $group)) {
//            $template = $this->getFieldsetTemplate($fieldset, $group);
//            if ($template !== $this->getTemplate()) {
//                $id = strtolower(preg_replace('/[^a-z0-9]/i', '-', $fieldset));
//                $template->setAttr('fields', 'id', $this->getForm()->makeInstanceKey('fs-' . $id));
//                $template->setAttr('fields', 'data-name', $id);
//                $template->addCss('fields', 'fs-' . $id);
//            }
//        }
//        $template = $this->getFieldsetTemplate($fieldset, $group);
//
//        if ($template->hasVar('legend')) {
//            $template->setText('legend', $fieldset);
//        }
//
//        /** @var Form\Field\FieldInterface $field */
//        foreach ($fields as $field) {
//            $template->setAttr('fields', $field->getFieldsetAttr()->getAttrList());
//            $ftpl = $this->showField($field);
//            if ($ftpl) $template->appendTemplate('fields', $ftpl);
//            //$template->appendTemplate('fields', $field->show());
//        }
//
//        return $template;
//    }


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
        return $sets;
    }

//    public function hasGroupTemplate(string $group): bool
//    {
//        return isset($this->groupTemplates[$group]);
//    }
//
//    public function getGroupTemplate(string $group): string
//    {
//        if (!$this->hasGroupTemplate($group)) {
//            $this->groupTemplates[$group] = $this->getTemplate()->getRepeat('group-' . $group);
//            if (!$this->hasGroupTemplate($group)) {
//                $this->groupTemplates[$group] = $this->buildTemplate('group-' . $group);
//            }
//            if (!$this->hasGroupTemplate($group)) {
//                $this->groupTemplates[$group] = $this->buildTemplate('group');
//            }
//            if (!$this->hasGroupTemplate($group)) return $this->getTemplate();
//        }
//        return $this->groupTemplates[$group];
//    }
//
//    public function hasFieldsetTemplate(string $fieldset, string $group = ''): bool
//    {
//        return isset($this->fieldsetTemplates[$group][$fieldset]);
//    }
//
//    public function getFieldsetTemplate(string $fieldset, string $group = ''): string
//    {
//        if (!$this->hasFieldsetTemplate($fieldset, $group)) {
//            $this->groupTemplates[$group] = $this->getTemplate()->getRepeat('fieldset-' . $fieldset);
//            if (!$this->hasFieldsetTemplate($fieldset, $group)) {
//                $this->fieldsetTemplates[$group][$fieldset] = $this->buildTemplate('fieldset-' . $fieldset);
//            }
//            if (!$this->hasFieldsetTemplate($fieldset, $group)) {
//                $this->fieldsetTemplates[$group][$fieldset] = $this->buildTemplate('fieldset');
//            }
//            if (!$this->hasFieldsetTemplate($fieldset, $group)) return $this->getTemplate();
//        }
//        return $this->fieldsetTemplates[$group][$fieldset];
//    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setForm(Form $form): static
    {
        $this->form = $form;
        return $this;
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

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $name, mixed $default = null): mixed
    {
        return $this->params[$name] ?? $default;
    }
}