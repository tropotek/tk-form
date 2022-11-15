<?php
use Tk\Config;

return function (Config $config)
{
    $config->set('path.template.form',        '/vendor/ttek/tk-form/templates/Form.html');
    $config->set('path.template.form.inline', '/vendor/ttek/tk-form/templates/FormInline.html');
};