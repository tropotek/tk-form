<?php
namespace Tk\Form;

use Dom\Builder;
use Dom\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tk\Form\Event\FormEvent;
use Tk\Form;
use Tk\Traits\SystemTrait;

/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class Renderer extends \Dom\Renderer\Renderer
{
    use SystemTrait;

    protected Form $form;

    protected ?EventDispatcherInterface $dispatcher;

    protected array $tabGroupTemplates = [];

    protected array $fieldsetTemplates = [];

    protected Builder $builder;


    public function __construct(Form $form, string $tplFile)
    {
        $this->form = $form;
        $this->dispatcher = $this->getFactory()->getEventDispatcher();
        $this->builder = new Builder($tplFile);
        $this->init();
    }

    protected function init()
    {
        $this->setTemplate($this->builder->getTemplate('tpl-form'));
        /** @var Form\Field\FieldInterface $field */
        foreach ($this->getForm()->getFieldList() as $field) {
            if ($field->hasTemplate()) continue;
            $field->setTemplate($this->buildTemplate($field->getType()));
        }
    }

    public function buildTemplate(string $fieldType): ?Template
    {
        $tpl = $this->builder->getTemplate('tpl-' . $fieldType);
        if (!$tpl) {
            $tpl = $this->builder->getTemplate('tpl-input');
        }
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

    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    function show(): ?Template
    {
        if (!$this->hasTemplate()) throw new \Tk\Form\Exception('Form template not found!');
        $template = $this->getTemplate();

        $e = new FormEvent($this->getForm());
        $this->getForm()->getDispatcher()?->dispatch($e, FormEvents::FORM_SHOW_PRE);

        // Field name attribute
        $template->setAttr('form', 'id', $this->getForm()->getId());

        // All other attributes
        $template->setAttr('form' ,$this->getForm()->getAttrList());

        // Element css class names
        $template->addCss('form', $this->getForm()->getCssList());

        $this->showFields($template);

        $this->getForm()->getDispatcher()?->dispatch($e, FormEvents::FORM_SHOW);
        return $template;
    }

    /**
     * Render Fields
     */
    protected function showFields(Template $template)
    {
        /** @var Form\Field\FieldInterface $field */
        foreach ($this->form->getFieldList() as $row => $field) {
            if ($field instanceof Form\Action\ActionInterface) {
                $template->appendTemplate('actions', $field->show());
            } else {
                $template->appendTemplate('fields', $field->show());
            }
        }
    }

}