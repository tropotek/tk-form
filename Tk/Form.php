<?php
namespace Tk;

use Tk\Form\Event\FormEvent;
use Tk\Form\Action\ActionInterface;
use Tk\Form\Field\FieldInterface;
use Tk\Form\FormEvents;
use Tk\Traits\EventDispatcherTrait;
use Tt\DataMap\Form\Value;


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
class Form extends Form\Element
{
    use EventDispatcherTrait;

    const ENCTYPE_URLENCODED        = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART         = 'multipart/form-data';
    const ENCTYPE_PLAIN             = 'text/plain';

    const METHOD_POST               = 'post';
    const METHOD_GET                = 'get';


    protected string $id = '';

    protected array $fields = [];

    protected ?ActionInterface $triggeredAction = null;

    protected array $errors = [];


    public function __construct(string $formId = 'form', string $charset = 'UTF-8')
    {
        $this->setDispatcher($this->getFactory()->getEventDispatcher());
        $this->setName($formId);
        $this->setId($formId);
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
        $this->setAttr('id', $id);
        return $this;
    }

    /**
     * Process the form request.
     *
     * The values should be that from a get or post request
     * this is left up to the user to source and send through.
     */
    public function execute(array $values = []): static
    {
        // Find the triggered action
        foreach($this->getFields() as $field) {
            if (!$field instanceof ActionInterface) continue;
            if (array_key_exists($field->getId(), $values)) {
                $this->triggeredAction = $field;
                $this->triggeredAction->setValue($values[$field->getId()]);
                break;
            }
        }

        if (!$this->isSubmitted()) {
            $this->executeFields($values);
            return $this;
        }

        $e = new FormEvent($this);
        $this->getDispatcher()?->dispatch($e, FormEvents::FORM_LOAD_REQUEST);

        $this->setFieldValues($values);
        $this->executeFields($values);

        $e = new FormEvent($this);
        $this->getDispatcher()?->dispatch($e, FormEvents::FORM_SUBMIT);

        // get the triggered action, this also set up the form ready to fire an action if present.
        $this->getTriggeredAction()->execute($values);

        return $this;
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

    public function addFieldError(string $fieldName, string $msg = ''): static
    {
        $field = $this->getField($fieldName);
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
            $field = $this->getField($fieldName);
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
            if (!array_key_exists($field->getName(), $values)) {
                $field->setRequested(false);
                continue;
            }
            $field->setRequested(true);
            $field->setValue($values[$field->getName()]);
        }
        return $this;
    }

    /**
     * This will return an array of the field's values,
     * $search can be a regex string to filter value keys using preg_match()
     * or it can be an array of field names that will be returned
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
     * return an array of field native values from an object
     * use DataTypeInterface objects to convert values
     *
     * @todo This should be moved to a parent level form object (FormModel ? )
     *       when we refactor to make \Tk\Form a standalone lib
     */
    public function unmapValues(object $object): array
    {
        $vals = [];
        foreach ($this->getFields() as $field) {
            if ($field instanceof ActionInterface) continue;
            $type = $field->getDataType() ?? new Value($field->getName());
            $vals[$field->getName()] = $type->getColumnValue($object);
        }
        return $vals;
    }

    /**
     * load an object with field values mapped to their complex types
     *
     * @todo This should be moved to a parent level form object (FormModel ? )
     *       when we refactor to make \Tk\Form a standalone lib
     */
    public function mapValues(object &$object): static
    {
        $values = $this->getFieldValues();
        foreach ($this->getFields() as $field) {
            if ($field instanceof ActionInterface) continue;
            if (!$field->isRequested()) continue;
            $type = $field->getDataType() ?? new Value($field->getName());
            $type->loadObject($object, $values);
        }
        return $this;
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

    public function appendField(FieldInterface $field, string $refField = ''): FieldInterface
    {
        if ($this->getField($field->getName())) {
            throw new \Tk\Form\Exception("Field with name '{$field->getName()}' already exists.");
        }
        $field->setForm($this);

        $ref = $this->getField($refField);
        if ($ref instanceof FieldInterface) {
            $a = [];
            foreach ($this->fields as $k => $v) {
                $a[$k] = $v;
                if ($k === $refField) $a[$field->getName()] = $field;
            }
            $this->fields = $a;
        } else {
            $this->fields[$field->getName()] = $field;
        }
        return $field;
    }

    public function prependField(FieldInterface $field, string $refField = ''): FieldInterface
    {
        if ($this->getField($field->getName())) {
            throw new \Tk\Form\Exception("Field with name '{$field->getName()}' already exists.");
        }
        $field->setForm($this);

        $ref = $this->getField($refField);
        if ($ref instanceof FieldInterface) {
            $a = [];
            foreach ($this->fields as $k => $v) {
                if ($k === $refField) $a[$field->getName()] = $field;
                $a[$k] = $v;
            }
            $this->fields = $a;
        } else {
            $this->fields = [$field->getName() => $field] + $this->fields;
        }
        return $field;
    }

    public function removeField(string $fieldName): ?FieldInterface
    {
        $field = $this->getField($fieldName);
        if (array_key_exists($fieldName, $this->fields)) {
            unset($this->fields[$fieldName]);
        }
        return $field;
    }

    public function getField(string $fieldName): ?FieldInterface
    {
        return $this->fields[$fieldName] ?? null;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getFieldValue(string $fieldName): mixed
    {
        $field = $this->getField($fieldName);
        return $field?->getValue();
    }

    public function setFieldValue(string $fieldName, mixed $value): ?FieldInterface
    {
        $field = $this->getField($fieldName);
        $field?->setValue($value);
        return $field;
    }

}
