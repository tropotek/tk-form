<?php
namespace Tk\Form;

use Tk\Form;
use Tk\InstanceKey;

abstract class Element extends \Tk\Ui\Element implements InstanceKey
{

    protected ?Form $form = null;

    protected string $id = '';

    protected string $name = '';

    protected string $label = '';

    protected string $notes = '';

    protected array $params = [];


    /**
     * The parent form should call all child fields and action execute() methods
     * once called.
     */
    abstract public function execute(array $values = []): static;

    /**
     * Create a label from a name string
     * The default label uses the name (EG: `fieldNameSelect` -> `Field Name Select`)
     */
    public static function makeLabel(string $name): string
    {
        $label = $name;
        $label = str_replace(['_', '-'], ' ', $label);
        $label = ucwords(preg_replace('/[A-Z]/', ' $0', $label));
        $label = preg_replace('/(\[\])/', '', $label);
        if (str_ends_with($label, 'Id')) {
            $label = substr($label, 0, -3);
        }
        return $label;
    }

    /**
     * Set the form for this element
     * NOTE: Be sure to set the form before setting any other elements like name/id etc...
     */
    public function setForm(Form $form): static
    {
        $this->form = $form;
        if (!$this->getId()) {
            $id = $this->getName();
            if (!$this instanceof Form) {
                $id = $this->makeInstanceKey($this->getName());
            }
            $this->setId($id);
            $this->setLabel(self::makeLabel($this->getName()));
        }
        return $this;
    }

    /**
     * Get the parent form element
     */
    public function getForm(): ?Form
    {
        return $this->form;
    }

    /**
     * Set the name for this element
     * This will initialise the element as it should be set after creation
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the unique name for this element
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Create request keys with prepended string
     *
     * returns: `{formId}-{$key}`
     *
     * The form->id is used as the instance key and must exist
     *   otherwise the key is returned unmodified.
     */
    public function makeInstanceKey(string $key): string
    {
        if ($this->getForm()) {
            return $this->getForm()->getId() . '-' . $key;
        }
        return $key;
    }

    /**
     * Setting the id also adds/updates the id value in the attributes array
     */
    protected function setId(string $id): static
    {
        $this->id = $id;
        $this->setAttr('id', $id);
        return $this;
    }

    /**
     * return the id of this element
     */
    public function getId(): string
    {
        return $this->getAttr('id');
    }

    /**
     * Get the label of this field
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set the label of this field
     */
    public function setLabel(string $str): static
    {
        $this->label = $str;
        return $this;
    }

    /**
     * Set the notes html/text
     */
    public function setNotes(string $html): static
    {
        $this->notes = $html;
        return $this;
    }

    /**
     * Get any notes on this element
     */
    public function getNotes(): string
    {
        return $this->notes;
    }


    public function setParam(string $name, mixed $value): static
    {
        $this->params[$name] = $value;
        return $this;
    }

    public function getParam(string $name, mixed $default = null): mixed
    {
        return $this->params[$name] ?? $default;
    }

    public function hasParam(string $name): bool
    {
        return array_key_exists($name, $this->params);
    }

    public function replaceParams(array $params): static
    {
        $this->params = $params;
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}