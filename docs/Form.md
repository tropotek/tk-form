# Tk\Form Developer Guide

This guide documents the public API of Tk\Form and shows typical usage patterns.

Note: Namespaced class is Tk\Form; it extends Tk\Form\Element and interacts with Tk\Form\Field\FieldInterface and Tk\Form\Action\ActionInterface.

## Construction

- __construct(?string $formId = null)
    - Creates a form with a unique id and name (default "form"). Sets method POST, empty action, and accept-charset.

Example:
```php
<?php
use Tk\Form;

$form = new Form('user-edit'); // id/name "user-edit"
```


## Submission lifecycle

- execute(array $values = []): static
    - Populates hidden meta fields (_formid and optional CSRF), detects triggered action, validates CSRF (for POST with ttl), loads values into fields, runs field execute(), and triggers the action callback.

Typical usage in a controller:
```php
<?php
$form = new \Tk\Form('contact');
$form->setCsrfTtl(\Tk\Form::DEFAULT_CSRF_TTL);
// append fields...
// $request contains merged GET/POST
$form->execute($request);

if ($form->isSubmitted() && !$form->hasErrors()) {
    $data = $form->getFieldValues();
    // process data, then redirect
}
```


- isSubmitted(): bool
    - True when request method matches form method and hidden _formid equals the form id.

Example:
```php
if ($form->isSubmitted()) { /* handle submit */ }
```


- getTriggeredAction(): ?ActionInterface
    - Returns the action field (e.g., submit button) that was used to submit, after execute().

Example:
```php
$action = $form->getTriggeredAction();
if ($action && $action->getName() === 'save') { /* handle save */ }
```


## CSRF

- setCsrfTtl(int $seconds): Form
    - Enables CSRF by setting a TTL (>0). Applies to POST forms.

- getCsrfTtl(): int
- getCsrfId(): string
- clearCsrf(): static

Example:
```php
$form->setCsrfTtl(15 * 60); // 15 minutes
// To invalidate token (e.g., logout)
$form->clearCsrf();
```


## Fields management

- appendField(FieldInterface $field, string $after = ''): FieldInterface
    - Adds a field after an existing one (or at end).

- prependField(FieldInterface $field, string $before = ''): FieldInterface
    - Adds a field before an existing one (or at beginning).

- removeField(string $fieldName): ?FieldInterface
- getField(string $fieldName): ?FieldInterface
- getFields(): array<string, FieldInterface>

Example:
```php
use Tk\Form\Field\Input;
use Tk\Form\Field\Select;

$form->appendField(new Input('email'));
$form->appendField(new Select('role'), 'email'); // after email
$role = $form->getField('role');
$form->removeField('deprecatedField');
```


## Getting and setting values

- setFieldValues(array $values): static
    - Loads values into fields (respects persistent/requested flags managed by fields).

- getFieldValues(string|array|null $search = null): array
    - Returns values from non-readonly and non-disabled fields. Optionally filter:
        - Regex string: keys matched via preg_match
        - Array: only listed keys returned

- getFieldValue(string $fieldName): mixed
- setFieldValue(string $fieldName, mixed $value): ?FieldInterface

Examples:
```php
// Load from request or model
$form->setFieldValues([
  'email' => 'user@example.com',
  'name'  => 'Jane Doe',
]);

// Read all values
$data = $form->getFieldValues();

// Filtered by regex
$userOnly = $form->getFieldValues('/^user_/');

// Single field
$email = $form->getFieldValue('email');
$form->setFieldValue('role', 'admin');
```


## Errors

- hasErrors(): bool
    - True if form has errors or any field has an error.

- addError(string $error): static
- setErrors(array $errors): static
- getErrors(): array
- getAllErrors(): array
    - Returns form errors plus per-field errors keyed by field name.

- addFieldError(string $fieldName, string $msg = ''): static
- addFieldErrors(array $errors): static
    - $errors is map of field => message or list of messages (accepted by the field).

Examples:
```php
if ($form->isSubmitted()) {
    if (!$form->getFieldValue('email')) {
        $form->addFieldError('email', 'Email is required.');
    }
    if ($someGlobalIssue) {
        $form->addError('Submission failed. Try again.');
    }
    if (!$form->hasErrors()) {
        // success
    }
}

// Render errors
$formErrors = $form->getErrors();
$fieldErrors = $form->getAllErrors(); // includes field-level messages
```


## HTTP attributes

- getMethod(): string
- setMethod(string $method): static
    - One of METHOD_POST, METHOD_GET, METHOD_PUT, METHOD_DELETE.

- getAction(): string
- setAction(string|Uri $url): static

- getEncType(): string
- setEncType(string $enctype): static
    - Use ENCTYPE_MULTIPART for file uploads.

Examples:
```php
$form->setMethod(\Tk\Form::METHOD_POST)
     ->setAction('/user/save')
     ->setEncType(\Tk\Form::ENCTYPE_MULTIPART);
```


Constants:
- ENCTYPE_URLENCODED, ENCTYPE_MULTIPART, ENCTYPE_PLAIN
- METHOD_POST, METHOD_GET, METHOD_PUT, METHOD_DELETE
- DEFAULT_CSRF_TTL, CSRF_TOKEN, FORM_ID

## Practical end-to-end example

```php
<?php
use Tk\Form;
use Tk\Form\Field\Input;
use Tk\Form\Field\Checkbox;
use Tk\Form\Field\File;
use Tk\Form\Action\Submit;

$form = new Form('profile');
$form->setMethod(Form::METHOD_POST)
     ->setAction('/profile/update')
     ->setEncType(Form::ENCTYPE_MULTIPART)
     ->setCsrfTtl(Form::DEFAULT_CSRF_TTL);

// Fields
$form->appendField(new Input('name'));
$form->appendField(new Input('email'));
$form->appendField(new Checkbox('newsletter'));
$form->appendField(new File('avatar'));
$form->appendField(new Submit('save'));   // implements ActionInterface
$form->appendField(new Submit('cancel')); // another action

// Prefill
$form->setFieldValues(['email' => 'jane@example.com']);

// Handle request
$form->execute($_POST + $_GET);

if ($form->isSubmitted()) {
    if ($form->getTriggeredAction()?->getName() === 'cancel') {
        // redirect without changes
    } elseif (!$form->hasErrors()) {
        $data = $form->getFieldValues();
        // persist $data
    }
}
```


## Notes and tips

- Always call execute() once per request to prepare meta fields and handle CSRF/action logic.
- Use setEncType(ENCTYPE_MULTIPART) if any file inputs are present.
- CSRF applies only when method is POST and ttl > 0; for idempotent GET forms, ttl can remain 0.
- Use getFieldValues() filtering to build payloads for specific subsystems (e.g., /^user_/).