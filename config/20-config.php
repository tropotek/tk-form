<?php
use Tk\Config;

return function (Config $config)
{
    // Dom Form renderer
    $config->set('path.template.form.dom',        '/vendor/ttek/tk-form/templates/Dom/bootstrap5/Form.html');
    $config->set('path.template.form.dom.inline', '/vendor/ttek/tk-form/templates/Dom/bootstrap5/FormInline.html');
    // Standard Form renderer
    $config->set('path.template.form.std',        '/vendor/ttek/tk-form/templates/Std/bootstrap5/Form.php');
    $config->set('path.template.form.std.inline', '/vendor/ttek/tk-form/templates/Std/bootstrap5/FormInline.php');
};