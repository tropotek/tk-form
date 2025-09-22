<?php
include_once __DIR__ . '/_prepend.php';


//vd(\Tk\Uri::create().'');

$form = new \Tk\Form('html-form');

$form->setMethod('post');
$form->setAction(\Tk\Uri::create());
$form->setEncType(\Tk\Form::ENCTYPE_MULTIPART);


$form->appendField(new \Tk\Form\Field\Hidden('hiddenField', 'hiddenValue'));

$form->appendField(new \Tk\Form\Field\Input('name'))->setRequired();

$form->appendField(new \Tk\Form\Field\Input('email'));

$form->appendField(new \Tk\Form\Field\File('file'));

$form->appendField(new \Tk\Form\Field\Input('disabledField'))->setDisabled();

$form->appendField(new \Tk\Form\Field\Input('readonlyField'))->setReadonly();

$form->appendField(new \Tk\Form\Field\Textarea('message'));


$form->appendField(new \Tk\Form\Action\Submit('submit', 'onFormSubmit'));
$form->appendField(new \Tk\Form\Action\Link('cancel', \Tk\Uri::create()));

// Create the renderer after adding form fields
$renderer = new \Tk\Form\Renderer\Std\Renderer($form);

$formHtml = $renderer->show();


function onFormSubmit(\Tk\Form $form, \Tk\Form\Action\Submit $action) {
    error_log(print_r($_POST, true));
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
    <h1>HTML Form Example</h1>
    <div class="col-lg-8 px-0">
        <p>
            This form uses the standard HTML form renderer.
        </p>

        <hr class="col-1 my-4">

        <?= $formHtml ?>

    </div>
</div>

</body>
</html>
