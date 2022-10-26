<?php
namespace Tk\Form;

use Dom\Renderer\Traits\AttributesTrait;
use Dom\Renderer\Traits\CssTrait;
use Tk\Form;
use Tk\InstanceKey;
use Tk\Traits\SystemTrait;

/**
 * @author tropotek <http://www.tropotek.com/>
 */
abstract class Element implements InstanceKey
{
    use AttributesTrait;
    use CssTrait;
    use SystemTrait;


    protected ?Form $form = null;

    protected string $id = '';

    protected string $name = '';

    protected string $label = '';

    protected string $notes = '';


    /**
     * The parent form should call all child fields and action execute() methods
     * once called.
     */
    abstract public function execute(): void;


    /**
     * Create a label from a name string
     * The default label uses the name (EG: `fieldNameSelect` -> `Field Name Select`)
     */
    public static function makeLabel(string $name): string
    {
        $label = $name;
        $label = str_replace(array('_', '-'), ' ', $label);
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
     * @throws Exception
     */
    public function setName(string $name): static
    {
        if (!$this->getForm()) throw new Exception('Form must be set before calling modifiers.');
        $this->name = $name;
        if (!$this->getLabel()) {
            $this->setLabel(self::makeLabel($this->getName()));
            $this->setId($this->makeInstanceKey($this->getName()));
        }
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
        if ($this->getForm() && $this->getForm() !== $this) {
            return $this->getForm()->getId() . '-' . $key;
        }
        return $key;
    }

    /**
     * Setting the id also adds/updates the id value in the attributes array
     */
    public function setId(string $id): static
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
        return $this->id;
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

}