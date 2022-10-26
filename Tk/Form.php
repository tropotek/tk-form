<?php
namespace Tk;

use Tk\Event\FormEvent;
use Tk\Form\Action\ActionInterface;
use Tk\Form\Exception;
use Tk\Form\Field;
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
class Form extends Form\Element
{

    const ENCTYPE_URLENCODED        = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART         = 'multipart/form-data';
    const ENCTYPE_PLAIN             = 'text/plain';

    const METHOD_POST               = 'post';
    const METHOD_GET                = 'get';


    protected string $id = '';

    /**
     * @var array|FieldInterface[]
     */
    protected array $fieldList = [];

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
        $this->loadFields($values);

        // get the triggered event, this also set up the form ready to fire an event if present.
        $event = $this->getTriggeredEvent($values);
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
        $this->loadFields($values);
        $this->executeFields($values);

        if ($this->getDispatcher()) {
            $e = new FormEvent($this);
            $this->getDispatcher()->dispatch($e, FormEvents::FORM_SUBMIT);
        }

        $event?->execute($values);
    }

    /**
     * Loads the fields with values from the array.
     * EG:
     *   $array['field1'] = 'value1';
     */
    protected function loadFields(array $array): static
    {
        $array = array_merge($this->defaultValues, $array);
        foreach ($this->getFieldList() as $field) {
            if ($field instanceof ActionInterface) continue;
            $field->load($array);
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
    public function getTriggeredEvent(array $array = []): ?ActionInterface
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
        return $this->getTriggeredEvent() != null;
    }


    /**
     * @param FieldInterface $field
     * @param null|FieldInterface|string $refField
     * @return FieldInterface
     * @since 2.0.68
     */
    public function appendField(FieldInterface $field, $refField = null)
    {
        $field->setForm($this);
        if ($this->getFieldset())
            $field->setFieldset($this->getFieldset(), $this->getFieldsetCss());
        if ($this->getTabGroup())
            $field->setTabGroup($this->getTabGroup(), $this->getTabGroupCss());
        if (is_string($refField)) {
            $refField = $this->getField(str_replace('[]', '', $refField));
        }

        if (!$refField || !$refField instanceof FieldInterface) {
            $this->fieldList[$field->getName()] = $field;
        } else {
            $newArr = array();
            /** @var FieldInterface $f */
            foreach ($this->fieldList as $f) {
                $newArr[$f->getName()] = $f;
                if ($f === $refField) $newArr[$field->getName()] = $field;
            }
            $this->fieldList = $newArr;
        }
        return $field;
    }

    /**
     * @param FieldInterface $field
     * @param null|FieldInterface|string $refField
     * @return FieldInterface
     * @since 2.0.68
     */
    public function prependField(FieldInterface $field, $refField = null)
    {
        $field->setForm($this);
        if ($this->getFieldset())
            $field->setFieldset($this->getFieldset(), $this->getFieldsetCss());
        if ($this->getTabGroup())
            $field->setTabGroup($this->getTabGroup(), $this->getTabGroupCss());
        if (is_string($refField)) {
            $refField = $this->getField(str_replace('[]', '', $refField));
        }

        if (!$refField || !$refField instanceof FieldInterface) {
            $this->fieldList = array($field->getName() => $field) + $this->fieldList;
        } else {
            $newArr = array();
            /** @var FieldInterface $f */
            foreach ($this->fieldList as $f) {
                if ($f === $refField) $newArr[$field->getName()] = $field;
                $newArr[$f->getName()] = $f;
            }
            $this->fieldList = $newArr;
        }
        return $field;
    }

    /**
     * Remove a field from the form
     *
     * @param string $fieldName
     * @return FieldInterface|null returns null if not found
     */
    public function removeField($fieldName)
    {
        $field = $this->getField($fieldName);
        $fieldName = str_replace('[]', '', $fieldName);
        if (isset($this->fieldList[$fieldName])) {
            unset($this->fieldList[$fieldName]);
        }
        return $field;
    }

    /**
     * Return a field object or null if not found
     *
     * @param string $fieldName
     * @return null|FieldInterface|Form\Event\FieldInterface
     */
    public function getField($fieldName)
    {
        $f = null;
        $fieldName = str_replace('[]', '', $fieldName);
        if (array_key_exists($fieldName, $this->fieldList)) {
            $f = $this->fieldList[$fieldName];
        }
        return $f;
    }

    /**
     * Set the field array
     *
     * @param array $arr
     * @return $this
     */
    public function setFieldList($arr = array())
    {
        $this->fieldList = $arr;
        return $this;
    }

    /**
     * Get the field array
     *
     * @return array|Element[]|Field\FieldInterface[]
     */
    public function getFieldList()
    {
        return $this->fieldList;
    }

    /**
     * Returns a form field value. Returns NULL if no field exists
     *
     * @param string $fieldName The element type name.
     * @return string|array
     */
    public function getFieldValue($fieldName)
    {
        $fieldName = str_replace('[]', '', $fieldName);
        $field = $this->getField($fieldName);
        if ($field instanceof FieldInterface) {
            return $field->getValue();
        }
        return null;
    }

    /**
     * Sets the value of an element type.
     *
     * @param string $fieldName The field name.
     * @param mixed $value The field value.
     * @return FieldInterface
     * @throws Exception
     */
    public function setFieldValue($fieldName, $value)
    {
        $fieldName = str_replace('[]', '', $fieldName);
        $field = $this->getField($fieldName);
        if (!$field || !$field instanceof FieldInterface) {
            throw new Exception('Form Field not found: `' . $fieldName . '`');
        }
        $field->setValue($value);
        return $field;
    }

    /**
     * Does this form contain errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        /* @var $field FieldInterface */
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

    /**
     * Get all the errors associated with this forms request
     *
     * @return array
     */
    public function getAllErrors()
    {
        $e = $this->errors;
        /* @var $field FieldInterface */
        foreach($this->getFieldList() as $field) {
            if ($field->hasErrors()) {
                $e[$field->getName()] = $field->getErrors();
            }
        }
        return $e;
    }

    /**
     * Adds field error.
     *
     * If the field is not found in the form then the error message is set to
     * the form error message.
     *
     * If $msg is null the field's error list is cleared
     *
     * @param string $fieldName A field name.
     * @param string $msg The error message.
     */
    public function addFieldError($fieldName, $msg = '')
    {
        $fieldName = str_replace('[]', '', $fieldName);
        /* @var $field FieldInterface */
        $field = $this->getField($fieldName);
        if ($field) {
            $field->addError($msg);
        } else {
            $this->addError($msg);
        }
    }

    /**
     * Adds form field errors from a map of (field name, list of errors) message pairs.
     *
     * If the field is not found in the form then the error message is added to
     * the form error messages.
     *
     * @param array $errors
     */
    public function addFieldErrors($errors)
    {
        foreach ($errors as $fieldName => $errorList) {
            $fieldName = str_replace('[]', '', $fieldName);
            $field = $this->getField($fieldName);
            if (!$field) {
                $this->addError($errorList);
            } else {
                $field->addError($errorList);
            }
        }
    }

    /**
     * This will return an array of the field's values,
     *
     * @param null|array|string $regex A regular expression or array of field names to get
     * @return array
     */
    public function getValues($regex = null)
    {
        $array = array();
        /* @var $field FieldInterface */
        foreach ($this->getFieldList() as $field) {
            if ($field instanceof Event\FieldInterface) continue;
            if ($regex) {
                if (is_string($regex) && !preg_match($regex, $field->getName())) {
                    continue;
                } else if (is_array($regex) && !in_array($field->getName(), $regex)) {
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
