<?php

use Tk\Uri;
require_once(__DIR__ . '/_prepend.php');

$form = new \Tk\Form('html-form');

$form->setMethod('post');
$form->setAction(\Tk\Uri::create());
$form->setAttr('novalidate');


// Add form fields
$form->appendField(new \Tk\Form\Field\Hidden('hiddenField', 'hiddenValue'));

$form->appendField(new \Tk\Form\Field\Input('name'))
    ->setRequired()
    ->addFieldCss('col-6')
    ->setAttr('placeholder', 'Your name')
    ->addValidator(function(\Tk\Form\Field\Input $field) {
        if (empty($field->getValue())) {
            $field->setError('Name is required');
        }
    });

$form->appendField(new \Tk\Form\Field\Input('email', 'email'))
    ->addFieldCss('col-6')
    ->setAttr('placeholder', 'Your Email')
    ->addValidator(function(\Tk\Form\Field\Input $field) {
        if (!filter_var($field->getValue(), FILTER_VALIDATE_EMAIL)) {
            $field->setError('Invalid email address');
        }
    });

$form->appendField(new \Tk\Form\Field\Password('password'));

$form->appendField(new \Tk\Form\Field\File('file'))
    ->setMultiple();

$form->appendField(new \Tk\Form\Field\Input('disabledField'))
    ->setDisabled();

$form->appendField(new \Tk\Form\Field\Input('readonlyField'))
    ->setReadonly();

$form->appendField(new \Tk\Form\Field\InputButton('inputButton', 'OK'))
    ->setAttr('data-js-action', 'show-alert');

$form->appendField(new \Tk\Form\Field\InputGroup('inputGroup', '', '<i class="fas fa-edit"></i>'));

$options = ['op1' => 'checkbox 1', 'op2' => 'checkbox 2', 'op3' => 'checkbox 3'];
$form->appendField(new \Tk\Form\Field\Checkbox('checkboxGroup', $options));

$options = ['opt1' => 'Option 1', 'opt2' => 'Option 2', 'opt3' => 'Option 3'];
$form->appendField(new \Tk\Form\Field\Radio('radioGroup', $options));

$options = ['opt1' => 'Option 1', 'opt2' => 'Option 2', 'opt3' => 'Option 3'];
$form->appendField(new \Tk\Form\Field\Select('Colors', $options));

$form->appendField(new \Tk\Form\Field\Textarea('message'));

$form->appendField(new \Tk\Form\Field\Checkbox('agree', ['y' => 'I agree to the terms and conditions']))
    ->setLabel('');

$form->appendField(new \Tk\Form\Action\Submit('submit', 'onFormSubmit'));
$form->appendField(new \Tk\Form\Action\Link('cancel', \Tk\Uri::create()));


// Set default values for fields
$defaults = [
        'hiddenField' => 'hiddenDefault',
        'readonlyField' => 'readonlyDefault',
];
$form->setFieldValues($defaults);

// execute the form submission
$form->execute($_POST);


// Create the renderer after adding form fields
$renderer = new \Tk\Form\Renderer\Dom\Renderer($form);
$formHtml = $renderer->show();

/**
 * Callable to handle form submission and validation
 */
function onFormSubmit(\Tk\Form $form, \Tk\Form\Action\Submit $action) {

    /** @var \Tk\Form\Field\File $file */
    $file = $form->getField('file');

    if ($form->hasErrors()) {
        $form->addError('Form submission failed');
        return;
    }

    // show a success message after redirect
    $_SESSION['success'] = true;

    // redirect to the appropriate page
    $action->setRedirect(Uri::create());
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TK Form Examples</title>
    <?php include_once __DIR__ . '/inc/head.php'; ?>
</head>
<body>

<?php include_once __DIR__ . '/inc/nav.php'; ?>

<div class="container my-5">
    <h1>DOM Form Example</h1>
    <div class="px-0">
        <p>
            This form uses the <a href="https://domtemplate.tropotek.com/" target="_blank">DOM Template</a> form renderer.
            The DOM form template is located in <code>templates/bs5-dom.html</code> and uses Bootstrap 5 markup.
        </p>

        <hr class="col-1 my-4">

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">Form successfully submitted.</div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?= $formHtml ?>

        <hr class="col-1 my-4">
        <p>Source code:</p>
        <?php highlight_file(__FILE__) ?>
    </div>
</div>

</body>
</html>
