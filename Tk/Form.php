<?php
namespace Tk;

use Tk\Form\Event\FormEvent;
use Tk\Form\Action\ActionInterface;
use Tk\Form\Field\FieldInterface;
use Tk\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


/**
 * The dynamic form processor
 *
 * `enctype` Attribute Values:
 * <code>
 *              Value                    |                 Description
 * --------------------------------------|---------------------------------------
 *  application/x-www-form-urlencoded    |  All characters are encoded before sent (this is default)
 *  multipart/form-data                  |  No characters are encoded. This value is required when you are using forms that have a file upload control
 *  text/plain                           |  Spaces are conve
     *
     * rted to "+" symbols, but no special characters are encoded
 * </code>
 *
 * @author Tropotek <http://www.tropotek.com/>
 */
class Form extends Form\Element implements FormInterface
{

    const ENCTYPE_URLENCODED        = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART         = 'multipart/form-data';
    const ENCTYPE_PLAIN             = 'text/plain';

    const METHOD_POST               = 'post';
    const METHOD_GET                = 'get';


    protected string $id = '';

    protected Collection $fieldList;

    protected ?ActionInterface $triggeredAction = null;

    protected ?EventDispatcherInterface $dispatcher = null;


    public function __construct(string $formId = 'form', string $charset = 'UTF-8')
    {
        $this->fieldList = new Collection();
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

    public function getDispatcher(): ?EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
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
        foreach($this->fieldList as $field) {
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
    }

    /**
     * Loads the fields with values from the array.
     * EG:
     *   $array['field1'] = 'value1';
     */
    public function setFieldValues(array $values): static
    {
        foreach ($this->getFieldList() as $field) {
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
        foreach ($this->getFieldList() as $field) {
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
        foreach ($this->getFieldList() as $field) {
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
        foreach ($this->getFieldList() as $field) {
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
        $field->setForm($this);
        return $this->getFieldList()->append($field->getName(), $field, $refField);
    }

    public function prependField(FieldInterface $field, ?string $refField = null): FieldInterface
    {
        $field->setForm($this);
        $this->getFieldList()->prepend($field->getName(), $field, $refField);
        return $field;
    }

    /**
     * Remove a field from the form
     */
    public function removeField(string $fieldName): ?FieldInterface
    {
        $field = $this->getFieldList()->get($fieldName);
        $this->getFieldList()->remove($fieldName);
        return $field;
    }

    /**
     * Return a field object or null if not found
     */
    public function getField(string $fieldName): ?FieldInterface
    {
        return $this->getFieldList()->get($fieldName);
    }

    /**
     * Get the field list Collection
     */
    public function getFieldList(): Collection
    {
        return $this->fieldList;
    }

    /**
     * Returns a form field value. Returns NULL if no field exists
     */
    public function getFieldValue(string $fieldName): mixed
    {
        $field = $this->getFieldList()->get($fieldName);
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
        $field = $this->getFieldList()->get($fieldName);
        $field?->setValue($value);
        return $field;
    }

    /**
     * Does this form contain errors
     */
    public function hasErrors(): bool
    {
        foreach ($this->fieldList as $field) {
            if ($field->hasError()) {
                return true;
            }
        }
        return false;
    }

    public function setErrors(array $errors): static
    {
        foreach($errors as $fieldName => $error) {
            if ($error) {
                $this->getField($fieldName)?->setError($error);
            }
        }
        return $this;
    }

    public function getErrors(): array
    {
        $e = [];
        foreach($this->getFieldList() as $field) {
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
        $field = $this->getFieldList()->get($fieldName);
        if ($field) {
            $field->addError($msg);
        }
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
            $field = $this->getFieldList()->get($fieldName);
            if ($field) {
                $field->addError($errorList);
            }
        }
        return $this;
    }
}
