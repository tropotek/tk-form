<?php
namespace Tk;

use Tk\Db\Model;
use Tk\Db\Session;
use Tk\Form\Action\ActionInterface;
use Tk\Form\Field\FieldInterface;
use Tk\Form\Field\Hidden;

class Form extends Form\Element
{
    public static string $CHARSET          = 'UTF-8';

    // All characters are encoded before sent (this is default)
    const string ENCTYPE_URLENCODED        = 'application/x-www-form-urlencoded';
    // No characters are encoded. This value is required when you are using forms that have a file upload control
    const string ENCTYPE_MULTIPART         = 'multipart/form-data';
    // Spaces are converted to "+" symbols, but no special characters are encoded
    const string ENCTYPE_PLAIN             = 'text/plain';

    const string METHOD_POST               = 'POST';
    const string METHOD_GET                = 'GET';
    const string METHOD_PUT                = 'PUT';
    const string METHOD_DELETE             = 'DELETE';

    /**
     * Default CSRF TTL seconds (15 mins)
     */
    const int    CSRF_TTL                  = 60*15;
    const string CSRF_TOKEN                = '_csrf_token';
    const string FORM_ID                   = '_formid';

    protected string $id      = '';
    protected array  $fields  = [];
    protected array  $errors  = [];
    protected int    $csrfTtl = self::CSRF_TTL;

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

    /**
     * The id can only be set once unless it is cleared first
     */
    protected function setId(string $id): static
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
        $this->appendField(new Hidden(self::FORM_ID, $this->getId()));

        // add csrf token
        if ($this->getMethod() == self::METHOD_POST && $this->csrfTtl > 0) {
            if (!Session::instance()->has($this->getCsrfId())) {
                $token = md5(uniqid());
                if (function_exists('openssl_random_pseudo_bytes')) {
                    $token = md5(openssl_random_pseudo_bytes(16));
                }
                Session::instance()->set($this->getCsrfId(), $token, $this->getCsrfTtl());
            }
            $this->appendField(new Hidden(self::CSRF_TOKEN, Session::instance()->get($this->getCsrfId(), '')));
        }

        if (!$this->isSubmitted()) {
            $this->executeFields($values);
            return $this;
        }

        // Find the triggered action
        foreach ($this->getFields() as $field) {
            if (!$field instanceof ActionInterface) continue;
            if (array_key_exists($field->getId(), $values)) {
                $this->triggeredAction = $field;
                $this->triggeredAction->setValue($values[$field->getId()]);
                break;
            }
        }


        // validate csrf_token
        if ($this->getMethod() == self::METHOD_POST && $this->csrfTtl > 0) {
            $token = trim(Session::instance()->get($this->getCsrfId(), ''));
            if (empty($token) || $values[self::CSRF_TOKEN] != $token) {
                Session::instance()->remove($this->getCsrfId());
                Alert::addError('Your form submission time has expired, please try again');
                Uri::create()->redirect();
            }
        }

        $this->setFieldValues($values);
        $this->executeFields($values);

        // get the triggered Form event action and execute callbacks if present.
        $this->getTriggeredAction()?->execute($values);

        return $this;
    }


    public function mapModel(Model $object): static
    {
        $values = $this->getFieldValues();
        $map = $object::getFormMap();
        $map->loadObject($object, $values);
        return $this;
    }

    public function unmapModel(Model $object): array
    {
        $map = $object::getFormMap();
        $values = [];
        $map->loadArray($values, $object);
        return $values;
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
            if (!($field->isPersistent() || array_key_exists($field->getName(), $values))) {
                $field->setRequested(false);
                continue;
            }
            $field->setRequested(true);
            $default = $field->isMultiple() ? [] : '';
            $field->setValue($values[$field->getName()] ?? $default);
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
            // remove values from array
            if ($field->isReadonly() || $field->isDisabled()) continue;
            if ($this->isSubmitted() && !$field->isRequested()) continue;

            $value = $field->getValue();
            if (!$field->isMultiple() && is_array($value)) {
                foreach ($value as $k => $v) {
                    $values[$k] = $v;
                }
            } else {
                $values[$field->getName()] = $value;
            }
        }

        // filter results using supplied filter param if exists
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
        return (
            (strtoupper($this->getMethod()) == $_SERVER['REQUEST_METHOD']) &&
            ($_POST[self::FORM_ID] ?? $_GET[self::FORM_ID] ?? '') == $this->getId()
        );
        //return $this->getTriggeredAction() != null;
    }

    public function clearCsrf(): static
    {
        Session::instance()->remove($this->getId().self::CSRF_TOKEN);
        return $this;
    }

    public function getCsrfId(): string
    {
        return $this->getId().self::CSRF_TOKEN;
    }

    /**
     * returns true on field and form errors
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

    /**
     * @return array<string,FieldInterface>
     */
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

    public function getMethod(): string
    {
        return strtoupper($this->getAttr('method', self::METHOD_POST));
    }

    public function setMethod(string $method): static
    {
        $this->setAttr('method', $method);
        return $this;
    }

    public function getAction(): string
    {
        return $this->getAttr('action', '');
    }

    public function setAction(string|Uri $url): static
    {
        $this->setAttr('action', $url);
        return $this;
    }

    public function getEncType(): string
    {
        return $this->getAttr('enctype', '');
    }

    public function setEncType(string $enctype): static
    {
        $this->setAttr('enctype', $enctype);
        return $this;
    }

    public function getCsrfTtl(): int
    {
        return $this->csrfTtl;
    }

    /**
     * Set the CSRF token time to live in seconds
     * Default: 15 mins
     */
    public function setCsrfTtl(int $seconds): Form
    {
        $this->csrfTtl = $seconds;
        return $this;
    }
}
