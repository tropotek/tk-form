<?php
namespace Tk\Form\Field;

use Dom\Renderer\Css;
use Dom\Renderer\RendererInterface;
use Dom\Renderer\Traits\RendererTrait;
use Tk\CallbackCollection;
use Tk\Form\Element;

/**
 *
 * @author Tropotek <http://www.tropotek.com/>
 */
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
    const TYPE_SELECT   = 'select';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_LINK     = 'link';
    const TYPE_BUTTON   = 'button';
    const TYPE_SUBMIT   = 'submit';

    protected mixed $value = '';

    protected string $type = '';

    protected string $error = '';

    protected CallbackCollection $onShow;


    // TODO: review if these should reside in the render system???

    protected string $fieldset = '';

    protected Css $fieldsetCss;

    protected string $tabGroup = '';


    public function __construct(string $name, string $type = 'text')
    {
        $this->fieldsetCss = new Css();
        $this->onShow = new CallbackCollection();
        $this->type = $type;
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
    public function setType(string $type): FieldInterface
    {
        $this->type = $type;
        return $this;
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

    public function getOnShow(): CallbackCollection
    {
        return $this->onShow;
    }

    /**
     * Callback: function (\Dom\Template $template, $element) { }
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