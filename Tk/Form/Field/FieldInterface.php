<?php
namespace Tk\Form\Field;

use Dom\Renderer\RendererInterface;
use Dom\Renderer\Traits\RendererTrait;
use Dom\Template;
use Tk\CallbackCollection;
use Tk\Form\Element;
use Tk\Ui\Attributes;
use Tk\Ui\Css;

abstract class FieldInterface extends Element implements RendererInterface
{
    use RendererTrait;

    /**
     * Some basic element types
     * for a full list of input types see: https://www.w3schools.com/tags/att_input_type.asp
     */
    const TYPE_NONE     = 'none';       // Use this when wanting to render the value as a html/text string not in an element
    const TYPE_HIDDEN   = 'hidden';
    const TYPE_TEXT     = 'text';
    const TYPE_PASSWORD = 'password';
    const TYPE_FILE     = 'file';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIO    = 'radio';
    const TYPE_HTML     = 'html';

    const TYPE_SELECT   = 'select';
    const TYPE_TEXTAREA = 'textarea';

    const TYPE_LINK     = 'link';
    const TYPE_BUTTON   = 'button';
    const TYPE_SUBMIT   = 'submit';

    const GROUP_NONE    = 'none';
    const GROUP_ACTIONS = 'actions';


    protected mixed $value = '';

    protected string $type = '';

    protected string $error = '';

    protected CallbackCollection $onShow;

    /**
     * attributes that affect the outer field parent template element
     */
    protected Attributes $fieldAttr;

    /**
     * Css that affect the outer field parent template element
     */
    protected Css $fieldCss;

    protected string $fieldset = '';

    protected Attributes $fieldsetAttr;

    /**
     * The group name could relate to a tab group, column group, etc
     * It will be up to the renderer where these are placed.
     * You may need to build a custom render to place the fields where you need them
     */
    protected string $group = '';

    protected Attributes $groupAttr;


    public function __construct(string $name, string $type = 'text')
    {
        $this->fieldAttr    = new Attributes();
        $this->fieldCss     = new Css();
        $this->groupAttr    = new Attributes();
        $this->fieldsetAttr = new Attributes();
        $this->onShow       = new CallbackCollection();
        $this->type         = $type;

        $this->setName($name);
        $this->setType($type);
    }

    /**
     * Called by the parent form when the request is executed.
     * Should be called after the form is initialised and loaded with values and before the
     * form is rendered.
     */
    public function execute(array $values = []): void { }

    /**
     * A basic common field renderer.
     */
    protected function decorate(Template $template): Template
    {
        if ($this->getNotes()) {
            $template->insertHtml('notes', $this->getNotes());
        }
        if ($this->hasError()) {
            if ($this->hasParam('error-css')) {
                $this->addCss($this->getParam('error-css'));
            }
            $template->insertHtml('error', $this->getError());
        } else {
//            if ($this->hasParam('valid-css')) {
//                $this->addCss($this->getParam('valid-css'));
//            }
        }

        $this->getOnShow()->execute($this, $template);

        // Add any attributes
        $template->addCss('field', $this->getFieldCss()->getCssList());
        $template->setAttr('field', $this->getFieldAttr()->getAttrList());
        $template->setAttr('element', $this->getAttrList());
        $template->addCss('element', $this->getCssList());

        // Render Label
        if($this->getLabel()) {
            $template->setText('label', $this->getLabel());
            $template->setAttr('label', 'for', $this->getId());
            $template->setVisible('label');
        }
        return $template;
    }

    /**
     * The value in a string format that can be rendered to the template
     * Recommended that values be PHP native types not objects, use the data mapper for complex types
     */
    public function setValue(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    /**
     * The value in a string/array format that can be rendered to the template
     * Recommended that values be PHP native types not objects, use the data mapper for complex typess
     */
    public function getValue(): mixed
    {
        return $this->value;
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
    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function setError(string $error): static
    {
        $this->error = $error;
        return $this;
    }

    public function hasError(): bool
    {
        return !empty($this->error);
    }

    public function getOnShow(): CallbackCollection
    {
        return $this->onShow;
    }

    /**
     * Callback: function (FieldInterface $element, Template $template) { }
     */
    public function addOnShow(callable $callable, int $priority = CallbackCollection::DEFAULT_PRIORITY): static
    {
        $this->getOnShow()->append($callable, $priority);
        return $this;
    }

    // Attribute helper methods

    /**
     * Does this fields data come as an array.
     * If the name ends in [] then it will be flagged as a multiple
     * EG: name=`name[]`
     */
    public function isMultiple(): bool
    {
        return $this->hasAttr('multiple');
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

    /**
     * Get the object for managing the field groups
     * attributes, use this to add attributes to the fields
     * root element, use setGroupAttr() to set an attribute
     */
    public function getFieldAttr(): Attributes
    {
        return $this->fieldAttr;
    }

    public function setFieldAttr(string $name, string $value): static
    {
        $this->fieldAttr->setAttr($name, $value);
        return $this;
    }

    public function addFieldCss(string $css): static
    {
        $this->fieldCss->addCss($css);
        return $this;
    }

    public function getFieldCss(): Css
    {
        return $this->fieldCss;
    }

    public function getFieldset(): string
    {
        return $this->fieldset;
    }

    public function getFieldsetAttr(): Attributes
    {
        return $this->fieldsetAttr;
    }

    public function setFieldset(string $fieldset, array $attrs = null): static
    {
        $this->fieldset = $fieldset;
        if ($attrs) {
            $this->getFieldsetAttr()->setAttr($attrs);
        }
        return $this;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function setGroup(string $group, array $attrs = null): static
    {
        $this->group = $group;
        if ($attrs) {
            $this->getGroupAttr()->setAttr($attrs);
        }
        return $this;
    }

    public function getGroupAttr(): Attributes
    {
        return $this->groupAttr;
    }

    protected function cleanName(string $str, string $replace = '-'): string
    {
        return preg_replace('/[^a-z0-9]/i', $replace, $str);
    }

}