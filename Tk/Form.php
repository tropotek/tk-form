<?php
namespace Tk;

use Tk\Form\Action\ActionInterface;
use Tk\Form\Field\FieldInterface;
use Tk\DataMap\Form\Value;

/**
 * TODO: document using the form
 *
 *
 *
 */
class Form extends Form\Element
{

    public static string $CHARSET   = 'UTF-8';

    // All characters are encoded before sent (this is default)
    const ENCTYPE_URLENCODED        = 'application/x-www-form-urlencoded';
    // No characters are encoded. This value is required when you are using forms that have a file upload control
    const ENCTYPE_MULTIPART         = 'multipart/form-data';
    // Spaces are converted to "+" symbols, but no special characters are encoded
    const ENCTYPE_PLAIN             = 'text/plain';

    const METHOD_POST               = 'post';
    const METHOD_GET                = 'get';
    const METHOD_PUT                = 'put';
    const METHOD_DELETE             = 'delete';

    protected string $id      = '';
    protected array  $fields  = [];
    protected array  $errors  = [];

    protected ?ActionInterface $triggeredAction = null;


    public function __construct(string $formId = 'form')
    {
        $this->setName($formId);
        $this->setId($formId);
        $this->setForm($this);
        $this->setMethod(self::METHOD_POST);
        $this->setAction(Uri::create());
        $this->setAttr('accept-charset', self::$CHARSET);
    }

    public static function create(string $formId = 'form'): static
    {
        return new static($formId);
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

        // todo implement our own event system for the form
//        $e = new FormEvent($this);
//        $this->getDispatcher()?->dispatch($e, FormEvents::FORM_LOAD_REQUEST);

        $this->setFieldValues($values);
        $this->executeFields($values);

//        $e = new FormEvent($this);
//        $this->getDispatcher()?->dispatch($e, FormEvents::FORM_SUBMIT);

        // get the triggered Form event action and execute callbacks if present.
        $this->getTriggeredAction()?->execute($values);

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
            $field?->setError($errorList);
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
        $values = [];

        /* @var $field FieldInterface */
        foreach ($this->getFields() as $field) {
            if ($field instanceof ActionInterface) continue;
            $value = $field->getValue();
            if (!$field->isMultiple() && is_array($value)) {
                foreach ($value as $k => $v) {  // pull values out if the element is not an array
                    $values[$k] = $v;
                }
            } else {
                $values[$field->getName()] = $value;
            }
        }

        // filter results
        if (!is_null($search)) {
            $a = [];
            if (is_string($search)) {
                foreach ($values as $k => $v) {
                    if (!preg_match($search, $k)) continue;
                    $a[$k] = $v;
                }
            } elseif (is_array($search)) {
                foreach ($values as $k => $v) {
                    if (!in_array($k, $search)) continue;
                    $a[$k] = $v;
                }
            }
            $values = $a;
        }

        return $values;
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
            if ($type->hasProperty($object)) {
                $vals[$field->getName()] = $type->getColumnValue($object);
            }
        }
        return $vals;
    }

    /**
     * load an object with field values mapped to their complex types
     *
     * @todo This should be moved to a parent level form object (FormModel ? )
     *       when we refactor to make \Tk\Form a standalone lib
     */
    public function mapValues(object $object): static
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

    public function setMethod(string $method): static
    {
        $this->setAttr('method', $method);
        return $this;
    }

    public function setAction(string|Uri $url): static
    {
        $this->setAttr('action', $url);
        return $this;
    }

    public function setEncType(string $enctype): static
    {
        $this->setAttr('enctype', $enctype);
        return $this;
    }
}
