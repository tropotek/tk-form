<?php
use Tk\Config;

return function (Config $config)
{
    // Dom Form renderer
    $config->set('path.template.form.dom',        '/vendor/ttek/tk-form/templates/dom/bootstrap5/form.html');
    $config->set('path.template.form.dom.inline', '/vendor/ttek/tk-form/templates/dom/bootstrap5/form_inline.html');
    // Standard Form renderer
    $config->set('path.template.form.std',        '/vendor/ttek/tk-form/templates/std/bootstrap5/form.php');
    $config->set('path.template.form.std.inline', '/vendor/ttek/tk-form/templates/std/bootstrap5/form_inline.php');
};