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
 *  text/plain                           |  Spaces are converted to "+" symbols, but no special characters are encoded
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

    protected ?ActionInterface $triggeredEvent = null;

    protected array $defaultValues = [];

    protected ?EventDispatcherInterface $dispatcher = null;

    /**
     * set to true after initForm() is called
     * to avoid duplicate init calls
     */
    private bool $initiated = false;


    public function __construct(string $formId = 'form', string $charset = 'UTF-8')
    {
        $this->fieldList = new Collection();
        $this->setForm($this);
        $this->setName($formId);
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

    public function isInitiated(): bool
    {
        return $this->initiated;
    }

    /**
     * Useful for extended form objects
     * To be called after all fields are added and
     */
    public function initForm()
    {
        if ($this->initiated) return;
        $this->initiated = true;
        if ($this->getDispatcher()) {
            $e = new FormEvent($this);
            $this->getDispatcher()->dispatch($e, FormEvents::FORM_INIT);
        }
    }

    /**
     * If an Action event is found its event is executed the result is returned
     */
    public function execute(array $values = []): void
    {
        //$this->initForm();      // TODO: not sure if this is a better place for it or not???

        // Load default field values
        //$this->setDefaultValues($this->defaultValues);
        if ($this->getDispatcher()) {
            $e = new FormEvent($this);
            $this->getDispatcher()->dispatch($e, FormEvents::FORM_LOAD);
        }
        $this->loadValues($values);

        // get the triggered event, this also set up the form ready to fire an event if present.
        $event = $this->getTriggeredAction($values);
        if (!$this->isSubmitted()) {
            $this->executeFields($values);
            return;
        }

        // Load request field values
        //$values = $this->cleanLoadArray($values);
        //$this->setDefaultValues($values);
        if ($this->getDispatcher()) {
            $e = new FormEvent($this);
            $this->getDispatcher()->dispatch($e, FormEvents::FORM_LOAD_REQUEST);
        }
        $this->loadValues($values);
        $this->executeFields($values);

        if ($this->getDispatcher()) {
            $e = new FormEvent($this);
            $this->getDispatcher()->dispatch($e, FormEvents::FORM_SUBMIT);
        }
        if ($event) $event->execute($values);
        //$event?->execute($values);
    }

    /**
     * Loads the fields with values from the array.
     * EG:
     *   $array['field1'] = 'value1';
     */
    public function loadValues(array $values): static
    {
        $values = array_merge($this->defaultValues, $values);
        foreach ($this->getFieldList() as $field) {
            if ($field instanceof ActionInterface) continue;
            $field->load($values);
        }
        return $this;
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
     * Clean the load() array
     *  o create a new raw array for any ArrayAccess objects like the request object
     *  o add array keys that the request modifies (request replaces '.' with '_') with field names
     *    this will not modify keys that a field does not exist for.
     *  @todo: See if this is required anymore
     */
//    protected function cleanLoadArray(array $array)
//    {
//        // Fix keys for conversions of '_' to '.' for fields that have been modified
//        /* @var $field FieldInterface */
//        foreach ($this->getFieldList() as $field) {
//            $cleanName = str_replace('.', '_', $field->getName());
//            if (array_key_exists($cleanName, $array) && !array_key_exists($field->getName(), $array)) {
//                $array[$field->getName()] = $array[$cleanName];
//            }
//
//            // TODO HACK: Trying to fix the issue when no field data is sent and then only the default field values exist
//            // TODO HACK: This is a mess, we need to go back to the drawing board on how we handle a request
//            // TODO HACK:   the main issue is the ability to call load() multiple times, then the request array
//            // TODO HACK:   when a value is null is ignored and only the loaded values exist when they should be
//            // TODO HACK:   set to null
//            // TODO HACK: The code below tries to fix this, I need to test a number of forms to ensure it does
//            // TODO HACK:   not have any unexpected consequences when saving field data
//            // TODO HACK:
//            if ($field->isReadonly() || $field->isDisabled()) continue;
//            if (!array_key_exists($field->getName(), $array)) {
//                $array[$field->getName()] = null;
//            }
//            // TODO HACK END:
//
//        }
//
//        return $array;
//    }

    /**
     * Get the field event to execute
     *
     * This will only return a valid value <b>after</b> the
     *   execute() method has been called.
     */
    public function getTriggeredAction(array $array = []): ?ActionInterface
    {
        if (!$this->triggeredEvent) {
            foreach($this->fieldList as $field) {
                if (!$field instanceof ActionInterface) continue;
                if (array_key_exists($field->getEventName(), $array)) {
                    $this->triggeredEvent = $field;
                    break;
                }
            }
        }
        return $this->triggeredEvent;
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
        if ($field) $field->setValue($value);
        //$field?->setValue($value);
        return $field;
    }

    /**
     * Does this form contain errors
     */
    public function hasErrors(): bool
    {
        foreach ($this->fieldList as $field) {
            if ($field->hasErrors()) {
                return true;
            }
        }
        if (count($this->getErrors())) {
            return true;
        }
        return false;
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
     *
     * @param array $errors
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

    /**
     * This will return an array of the field's values,
     */
    public function getValues(string|array|null $search = null): array
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

}
