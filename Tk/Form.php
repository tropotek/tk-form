<?php
namespace Tk;

use Tk\Form\Event\FormEvent;
use Tk\Form\Action\ActionInterface;
use Tk\Form\Field\FieldInterface;
use Tk\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tk\Traits\EventDispatcherTrait;


/**
 * The dynamic form processor
 *
 * `enctype` Attribute Values:
 * <code>
 *              Value                    |                 Description
 * --------------------------------------|---------------------------------------
 *  application/x-www-form-urlencoded    |  All characters are encoded before sent (this is default)
 *  multipart/form-data                  |  No characters are encoded. This value is required when you are using forms that have a file upload control
 *  text/plain                           |  Spaces are converted to "+" symbols, but no special characters are encoded
 * </code>
 *
 */
class Form extends Form\Element implements FormInterface
{
    use EventDispatcherTrait;

    const ENCTYPE_URLENCODED        = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART         = 'multipart/form-data';
    const ENCTYPE_PLAIN             = 'text/plain';

    const METHOD_POST               = 'post';
    const METHOD_GET                = 'get';


    protected string $id = '';

    protected Collection $fields;

    protected ?ActionInterface $triggeredAction = null;

    protected array $errors = [];


    public function __construct(string $formId = 'form', string $charset = 'UTF-8')
    {
        $this->fields = new Collection();
        $this->setDispatcher($this->getFactory()->getEventDispatcher());
        $this->setName($formId);
        $this->setForm($this);
        $this->setAttr('method', self::METHOD_POST);
        $this->setAttr('action', Uri::create());
        $this->setAttr('accept-charset', $charset);
    }

    public static function create(string $formId = 'form', string $charset = 'UTF-8'): static
    {
        return new static($formId, $charset);
    }

    /**
     * The id can only be set once unless it is cleared first
     */
    protected function setId($id): static
    {
        static $instances = [];
        if ($this->getId()) return $this;
        if (!isset($instances[$id])) {
            $instances[$id] = 0;
        } else {
            $instances[$id]++;
        }
        if ($instances[$id] > 0) $id = $id.'-'.$instances[$id];
        $this->id = $id;
        $this->setAttr('id', $this->getId());
        return $this;
    }

    /**
     * Process the form request.
     *
     * The values should be that from a get or post request
     * this is left up to the user to source and send through.
     */
    public function execute(array $values = []): void
    {
        // Find the triggered action
        foreach($this->getFields() as $field) {
            if (!$field instanceof ActionInterface) continue;
            if (array_key_exists($field->getId(), $values)) {
                $this->triggeredAction = $field;
                break;
            }
        }

        if (!$this->isSubmitted()) {
            $this->executeFields($values);
            return;
        }

        $e = new FormEvent($this);
        $this->getDispatcher()?->dispatch($e, FormEvents::FORM_LOAD_REQUEST);

        $this->setFieldValues($values);
        $this->executeFields($values);

        $e = new FormEvent($this);
        $this->getDispatcher()?->dispatch($e, FormEvents::FORM_SUBMIT);

        // get the triggered action, this also set up the form ready to fire an action if present.
        $this->getTriggeredAction()->execute($values);
        $url = $this->getTriggeredAction()->getRedirect();
    }

    /**
     * Does this form contain errors
     */
    public function hasErrors(): bool
    {
        if (count($this->getErrors())) return true;
        foreach ($this->getFields() as $field) {
            if ($field->hasError()) {
                return true;
            }
        }
        return false;
    }

    public function addError(string $error): static
    {
        if (trim($error)) {
            $this->errors[] = trim($error);
        }
        return $this;
    }

    public function setErrors(array $errors): static
    {
        $this->errors = $errors;
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getAllErrors(): array
    {
        $e = $this->getErrors();
        foreach($this->getFields() as $field) {
            if ($field->hasError()) {
                $e[$field->getName()] = $field->getError();
            }
        }
        return $e;
    }

    /**
     * Adds field error message.
     */
    public function addFieldError(string $fieldName, string $msg = ''): static
    {
        /** @var FieldInterface $field */
        $field = $this->getFields()->get($fieldName);
        $field?->setError($msg);
        return $this;
    }

    /**
     * Adds form field errors from a map of (field name, list of errors) message pairs.
     *
     * If the field is not found in the form then the error message is added to
     * the form error messages.
     */
    public function addFieldErrors(array $errors): static
    {
        foreach ($errors as $fieldName => $errorList) {
            $field = $this->getFields()->get($fieldName);
            if ($field) {
                $field->setError($errorList);
            }
        }
        return $this;
    }

    /**
     * Loads the fields with values from the array.
     * EG:
     *   $array['field1'] = 'value1';
     */
    public function setFieldValues(array $values): static
    {
        /** @var FieldInterface $field */
        foreach ($this->getFields() as $field) {
            if ($field instanceof ActionInterface) continue;
            $field->setValue($values[$field->getName()] ?? '');
        }
        return $this;
    }

    /**
     * This will return an array of the field's values,
     */
    public function getFieldValues(string|array|null $search = null): array
    {
        $array = [];
        /* @var $field FieldInterface */
        foreach ($this->getFields() as $field) {
            if ($field instanceof ActionInterface) continue;
            if ($search) {
                if (is_string($search) && !preg_match($search, $field->getName())) {
                    continue;
                } else if (is_array($search) && !in_array($field->getName(), $search)) {
                    continue;
                }
            }
            $value = $field->getValue();

            if (!$field->isMultiple() && is_array($value)) {
                foreach ($value as $k => $v) {  // pull values out if the element is not an array
                    $array[$k] = $v;
                }
            } else {
                $array[$field->getName()] = $value;
            }
        }
        return $array;
    }

    /**
     * This is called after new data loaded into the fields
     */
    protected function executeFields(array $values): static
    {
        foreach ($this->getFields() as $field) {
            if ($field instanceof ActionInterface) continue;
            $field->execute($values);
        }
        return $this;
    }

    /**
     * Return all submit/button event fields
     *
     * @return ActionInterface[]|array
     */
    protected function getEventFields(): array
    {
        $list = [];
        foreach ($this->getFields() as $field) {
            if ($field instanceof ActionInterface) $list[] = $field;
        }
        return $list;
    }

    /**
     * Get the field event to execute
     *
     * This will only return a valid value <b>after</b> the
     *   execute() method has been called.
     */
    public function getTriggeredAction(): ?ActionInterface
    {
        return $this->triggeredAction;
    }

    /**
     * Check if the form has been submitted
     */
    public function isSubmitted(): bool
    {
        return $this->getTriggeredAction() != null;
    }

    public function appendField(FieldInterface $field, ?string $refField = null): FieldInterface
    {
        if ($this->getFields()->has($field->getName())) {
            throw new \Tk\Table\Exception("Field with name '{$field->getName()}' already exists.");
        }
        $field->setForm($this);
        return $this->getFields()->append($field->getName(), $field, $refField);
    }

    public function prependField(FieldInterface $field, ?string $refField = null): FieldInterface
    {
        if ($this->getFields()->has($field->getName())) {
            throw new \Tk\Table\Exception("Field with name '{$field->getName()}' already exists.");
        }
        $field->setForm($this);
        $this->getFields()->prepend($field->getName(), $field, $refField);
        return $field;
    }

    /**
     * Remove a field from the form
     */
    public function removeField(string $fieldName): ?FieldInterface
    {
        $field = $this->getFields()->get($fieldName);
        $this->getFields()->remove($fieldName);
        return $field;
    }

    /**
     * Return a field object or null if not found
     */
    public function getField(string $fieldName): ?FieldInterface
    {
        return $this->getFields()->get($fieldName);
    }

    /**
     * Get the field list Collection
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    /**
     * Returns a form field value. Returns NULL if no field exists
     */
    public function getFieldValue(string $fieldName): mixed
    {
        $field = $this->getFields()->get($fieldName);
        if ($field) {
            return $field->getValue();
        }
        return null;
    }

    /**
     * Sets the value of an element type.
     */
    public function setFieldValue($fieldName, $value): ?FieldInterface
    {
        /** @var FieldInterface $field */
        $field = $this->getFields()->get($fieldName);
        $field?->setValue($value);
        return $field;
    }

}
