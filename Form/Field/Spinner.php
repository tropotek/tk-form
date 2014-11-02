<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 *  A JqUERY ui SPINNER FIELD
 *
 * culture, disabled, icons, incremental, max, min, numberFormat, page, step
 *
 *
 *
 * @see http://jqueryui.com/spinner/
 * @package Form\Field
 */
class Spinner extends Text
{

    protected $options = array();
    protected $opts = '';


    /**
     *
     * @param string $name
     * @param array $opts
     */
    public function __construct($name, $opts = array())
    {
        parent::__construct($name, new \Form\Type\Integer());
        $this->options = $opts;
        if (empty($this->options['min'])) $this->options['min'] = 0;
        if (empty($this->options['max'])) $this->options['max'] = 10;
        if (empty($this->options['step'])) $this->options['step'] = 1;
        if (empty($this->options['start'])) $this->options['start'] = 0;
        $this->addStyle('width', '80px');

        $this->opts = '';
        foreach ($this->options as $k => $v) {
            if (is_numeric($v) || $v == 'true' || $v == 'false') {
                $this->opts .= sprintf('%s: %s,', enquote($k), $v);
            } else {
                $this->opts .= sprintf('%s: %s,', enquote($k), enquote($v));
            }
        }

        if ($this->opts) {
            $this->opts = substr($this->opts, 0, -1);
        }
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
<script>
jQuery(function($) {
  $('.Spinner input').spinner({ {$this->opts} });
});
</script>
<input type="text" var="element" />
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }

}