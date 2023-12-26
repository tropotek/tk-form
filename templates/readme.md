# Tk Form Templates

This folder holds default form rederer templates.
Copy these to your site and update them to suit your site. 

You have the option to send the template path to the FormRenderer constructor or
alternativly add the config option to your \App\Config config file `/src/config/config.php`.
```php
    // Dom Form renderer
    $config->set('path.template.form.dom',        '/{srcPath}/templates/Dom/boostrap5/Form.html');
    $config->set('path.template.form.dom.inline', '/{srcPath}/templates/Dom/boostrap5/FormInline.html');
    // Standard Form renderer
    $config->set('path.template.form.std',        '/{srcPath}/templates/Std/boostrap5/Form.html');
    $config->set('path.template.form.std.inline', '/{srcPath}/templates/Std/boostrap5/FormInline.html');
```


