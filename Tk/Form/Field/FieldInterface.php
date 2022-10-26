<?php
namespace Tk\Form\Field;

use Dom\Renderer\Css;
use Tk\Form;
use Tk\Form\Element;
use Tk\Form\Exception;

/**
 *
 * @author Tropotek <http://www.tropotek.com/>
 */
abstract class FieldInterface extends Element
{
    /**
     * Some basic element types
     * for a full list of input types see: https://www.w3schools.com/tags/att_input_type.asp
     */
    const TYPE_NONE     = 'none';       // Use this when wanting to render the value as a html/text string not in an element
    const TYPE_BUTTON   = 'button';
    const TYPE_HIDDEN   = 'hidden';
    const TYPE_TEXT     = 'text';
    const TYPE_SELECT   = 'select';     // Special case, not <input>
    const TYPE_TEXTAREA = 'textarea';   // Special case, not <input>

    protected mixed $value = null;

    protected string $type = '';

    protected string $error = '';


    // TODO: review if these should reside in the render system???

    protected string $fieldset = '';

    protected Css $fieldsetCss;

    protected string $tabGroup = '';


    public function __construct(string $name, string $type = 'text')
    {
        $this->fieldsetCss = new Css();
        $this->setName($name);
        $this->setType($type);
    }

    /**
     * Assumes the field value resides within an array.
     * This objects load() method is called by the form's execute() method
     * Note: When the value does not exist it is ignored
     *     (may not be the desired result for unselected checkbox or empty select box)
     */
    public function load(array $values): static
    {
        if (array_key_exists($this->getName(), $values)) {
            $this->setValue($values[$this->getName()]);
        }
        return $this;
    }

    /**
     * Called by the parent form when the request is executed.
     * Should be called after the form is initialised and loaded with values and before the
     * form is rendered.
     */
    public function execute(): void { }

    /**
     * Set the name for this element
     *
     * When using the element with an array name (EG: 'name[]')
     * The '[]' are removed from the name but the isArray value is set to true.
     *
     * NOTE: only single dimensional numbered arrays are supported,
     *  Multidimensional or named arrays are not.
     *  Invalid field name examples are:
     *   o 'name[key]'
     *   o 'name[][]'
     *   o 'name[key][]'
     *
     * @throws Exception
     */
    public function setName(string $name): static
    {
        $n = $name;
        if (str_ends_with($n, '[]')) {
            $this->setMultiple(true);
            $n = substr($n, 0, -2);
        }
        parent::setName($n);
        return $this;
    }

    /**
     * Get the HTML unique name for this element
     */
    public function getHtmlName(): string
    {
        $n = $this->getName();
        if ($this->isMultiple()) {
            $n .= '[]';
        }
        return $n;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the type of element this is
     * However custom types are allowed, the TYPES_ constants are only common types
     * Custom types for your own renders are also allowed.
     *
     * @see: https://www.w3schools.com/tags/att_input_type.asp
     */
    public function setType(string $type): FieldInterface
    {
        $this->type = $type;
        return $this;
    }

    /**
     * The value should be of a type that can be handled by
     * this field`s data mappers
     */
    public function setValue(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    /**
     * The value should be of a type that can be handled by
     * this field`s data mappers
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function setError(string $error): FieldInterface
    {
        $this->error = $error;
        return $this;
    }

    public function hasError(): bool
    {
        return !empty($this->error);
    }

    // Attribute helper methods

    /**
     * Does this fields data come as an array.
     * If the name ends in [] then it will be flagged as a multiple
     * EG: name=`name[]`
     */
    public function isMultiple(): bool
    {
        return $this->hasAttr('disabled');
    }

    /**
     * Set to true if this element is an array set
     *
     * EG: name=`name[]`
     */
    public function setMultiple(bool $multiple): static
    {
        if ($multiple)
            $this->setAttr('multiple');
        else
            $this->removeAttr('multiple');
        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->hasAttr('disabled');
    }

    public function setDisabled(bool $disabled = true): static
    {
        if ($disabled)
            $this->setAttr('disabled');
        else
            $this->removeAttr('disabled');
        return $this;
    }

    public function isReadonly(): bool
    {
        return $this->hasAttr('readonly');
    }

    public function setReadonly(bool $readonly = true): static
    {
        if ($readonly)
            $this->setAttr('readonly');
        else
            $this->removeAttr('readonly');
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->hasAttr('required');
    }

    public function setRequired(bool $required = true): static
    {
        if ($required) {
            $this->setAttr('required');
        } else {
            $this->removeAttr('required');
        }
        return $this;
    }

    // TODO: Tabgroups and fieldsets lets see if this belongs here? they are a rendering item???

    public function getFieldset(): string
    {
        return $this->fieldset;
    }

    public function getFieldsetCss(): string
    {
        return $this->fieldsetCss->getCssString();
    }

    public function setFieldset(string $fieldset, string $css = ''): static
    {
        $this->fieldset = $fieldset;
        if ($css) {
            $this->fieldsetCss->addCss($css);
        }
        return $this;
    }

    public function getTabGroup(): string
    {
        return $this->tabGroup;
    }

    public function setTabGroup(string $tabGroup): static
    {
        $this->tabGroup = $tabGroup;
        return $this;
    }


}