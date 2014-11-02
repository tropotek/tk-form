<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 * This field is multiple file uploader for a form.
 *
 * @requires jQuery File Upload Plugin http://blueimp.github.io/jQuery-File-Upload/
 *
 */
class FileMultiple extends File
{




    /**
     *
     * @param type $name
     * @param type $list
     */
    public function __construct($name, $list)
    {
        parent::__construct($name, $list);
    }

    /**
     * Render the widget.
     *
     */
    public function show()
    {
        parent::show();
        $template = $this->getTemplate();

        //$template->appendJsUrl(\Tk\Url::create('/assets/tek-js/util.js'));
        
        $js = <<<JS


JS;
        $template->appendJs($js);

    }


    /**
     * makeTemplate
     *
     * @return string
     */
    public function __makeTemplate()
    {
        $xmlStr = <<<XML
<?xml version="1.0"?>
<div>
<script src="/assets/jquery/plugins/jquery.fileFix.js"></script>
<script>
jQuery(function($) {
  $('.tk-form .File input[type=file]').fileFix();
});
</script>
  <input type="file" var="element" />
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }

    
}