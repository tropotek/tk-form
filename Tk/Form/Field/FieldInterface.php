<?php
namespace Tk\Form\Field;

use Tk\CallbackCollection;
use Tk\Form\Element;
use Tk\Ui\Attributes;
use Tk\DataMap\DataTypeInterface;

abstract class FieldInterface extends Element
{

    /**
     * Some basic element types
     * for a full list of input types see: https://www.w3schools.com/tags/att_input_type.asp
     */
    const string TYPE_NONE     = 'none';       // Use this when wanting to render the value as a html/text string not in an element
    const string TYPE_HIDDEN   = 'hidden';
    const string TYPE_TEXT     = 'text';
    const string TYPE_PASSWORD = 'password';
    const string TYPE_FILE     = 'file';
    const string TYPE_CHECKBOX = 'checkbox';
    const string TYPE_RADIO    = 'radio';
    const string TYPE_HTML     = 'html';

    const string TYPE_SELECT   = 'select';
    const string TYPE_TEXTAREA = 'textarea';

    const string TYPE_LINK     = 'link';
    const string TYPE_BUTTON   = 'button';
    const string TYPE_SUBMIT   = 'submit';

    const string GROUP_NONE    = 'none';
    const string GROUP_ACTIONS = 'actions';


    protected string|array $value = '';
    protected string $type        = '';
    protected string $error       = '';
    protected string $group       = '';
    protected string $fieldset    = '';
    protected ?bool  $requested   = null;  // was this element passed in the request string

    /**
     * attributes that affect the outer parent elements
     */
    protected Attributes $fieldAttr;    // input parent element attrs
    protected Attributes $fieldsetAttr; // fieldset element attrs
    protected Attributes $groupAttr;    // group/tab element attrs
    protected CallbackCollection $onShow;


    public function __construct(string $name, string $type = 'text')
    {
        $this->fieldAttr    = new Attributes();
        $this->groupAttr    = new Attributes();
        $this->fieldsetAttr = new Attributes();
        $this->onShow       = new CallbackCollection();
        $this->type         = $type;

        $this->setName($name);
        $this->setType($type);
        $this->addFieldCss('fld fld-'.$this->getHtmlName() . ' fld-'.$this->getType());
    }

    /**
     * Called by the form when executed with a request/values array.
     */
    public function execute(array $values = []): static { return $this; }


    /**
     * the value can be an array for a radio/checkbox group or select field
     */
    public function setValue(string|array $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function getValue(): string|array
    {
        return $this->value;
    }

    /**
     * does value exist in the recent executed request
     * null = request not executed yet
     */
    public function isRequested(): ?bool
    {
        return $this->requested;
    }

    public function setRequested(?bool $requested): FieldInterface
    {
        $this->requested = $requested;
        return $this;
    }

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
     */
    public function setName(string $name): static
    {
        $n = $name;
        if (str_ends_with($n, '[]')) {
            $this->setMultiple(true);
            $n = substr($n, 0, -2);
        }
        parent::setName($n);
        $this->setAttr('name', $this->getHtmlName());
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

    /**
     * Set the type of element this is
     * However custom types are allowed, the TYPES_ constants are only common types
     * Custom types for your own renders are also allowed.
     *
     * @see: https://www.w3schools.com/tags/att_input_type.asp
     */
    public function setType(string $type): static
    {
        $this->type = $type;
        $this->setAttr('type', $type);
        return $this;
    }

    public function getType(): string
    {
        return $this->getAttr('type', '');
    }

    public function setError(string $error): static
    {
        $this->error = $error;
        return $this;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function hasError(): bool
    {
        return !empty($this->error);
    }

    /**
     * Callback: function (FieldInterface $element, Template $template) { }
     */
    public function addOnShow(callable $callable, int $priority = CallbackCollection::DEFAULT_PRIORITY): static
    {
        $this->getOnShow()->append($callable, $priority);
        return $this;
    }

    public function getOnShow(): CallbackCollection
    {
        return $this->onShow;
    }

    // Attribute helper methods

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

    /**
     * Does this fields data come as an array.
     * If the name ends in [] then it will be flagged as a multiple
     * EG: name=`name[]`
     */
    public function isMultiple(): bool
    {
        return $this->hasAttr('multiple');
    }

    public function setDisabled(bool $disabled = true): static
    {
        if ($disabled)
            $this->setAttr('disabled');
        else
            $this->removeAttr('disabled');
        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->hasAttr('disabled');
    }

    public function setReadonly(bool $readonly = true): static
    {
        if ($readonly)
            $this->setAttr('readonly');
        else
            $this->removeAttr('readonly');
        return $this;
    }

    public function isReadonly(): bool
    {
        return $this->hasAttr('readonly');
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

    public function isRequired(): bool
    {
        return $this->hasAttr('required');
    }

    public function setFieldAttr(string $name, string $value): static
    {
        $this->fieldAttr->setAttr($name, $value);
        return $this;
    }

    /**
     * Get the object for managing the field groups
     * attributes, use this to add attributes to the fields
     * root element, use setGroupAttr() to set an attribute
     */
    public function getFieldAttr(): Attributes
    {
        return $this->fieldAttr;
    }

    public function addFieldCss(string $css): static
    {
        $this->fieldAttr->addCss($css);
        return $this;
    }

    public function getFieldCss(): Attributes
    {
        return $this->fieldAttr;
    }

    public function setFieldset(string $fieldset, array $attrs = null): static
    {
        $this->fieldset = $fieldset;
        if ($attrs) {
            $this->getFieldsetAttr()->setAttr($attrs);
        }
        return $this;
    }

    public function getFieldset(): string
    {
        return $this->fieldset;
    }

    public function getFieldsetAttr(): Attributes
    {
        return $this->fieldsetAttr;
    }

    /**
     * The group name could relate to a tab group, column group, etc
     * It will be up to the renderer where these are placed.
     * You may need to build a custom render to place the fields where you need them
     */
    public function setGroup(string $group, array $attrs = null): static
    {
        $this->group = $group;
        if ($attrs) {
            $this->getGroupAttr()->setAttr($attrs);
        }
        return $this;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getGroupAttr(): Attributes
    {
        return $this->groupAttr;
    }

    public function cleanName(string $str, string $replace = '-'): string
    {
        return preg_replace('/[^a-z0-9]/i', $replace, $str);
    }

}