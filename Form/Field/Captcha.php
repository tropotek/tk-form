<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 *  A form text field object
 *
 * @package Form\Field
 */
class Captcha extends Iface
{

    /**
     * @var Captcha\Adapter
     */
    protected $adapter = null;


    /**
     * __construct
     *
     * @param string $name
     * @param Captcha\Adapter $ad
     */
    public function __construct($name, $ad = null)
    {
        parent::__construct($name);
        $this->setAutocomplete(false);
        if (!$ad) {
            $ad = new Captcha\Basic();
        }
        $this->adapter = $ad;
        $this->adapter->setField($this);
        $this->addFieldClass('vcheck');
    }

    /**
     * Get the adapter
     *
     * @return Captcha\Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Set the form for this element
     *
     * @param \Form\Form $form
     * @return \Form\Element
     */
    public function setForm(\Form\Form $form)
    {
        parent::setForm($form);
        $form->attach(new Capcha($this->adapter), \Form\Form::EVENT_PRE_SUBMIT);
        $form->attach(new CapchaEnd($this->adapter), \Form\Form::EVENT_POST_SUBMIT);
        return $this;
    }


    /**
     * show
     */
    public function show()
    {
        $t = $this->getTemplate();

        $t->setAttr('image', 'src', $this->adapter->getImageUrl());

        parent::show();
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
<div style="display: inline-block;">
<script>
//<![CDATA[
jQuery(function($) {
    var src = $('.tk-form .Captcha .reloadVcheck').parent().prev().attr('src');
    $('.tk-form .Captcha .reloadVcheck').click(function () {
        $(this).parent().prev().attr('src', src + '&r=' + (new Date().getTime()) );
    });
});
//]]>
</script>

  <input type="text" var="element" style="width: 80px;display: inline-block;margin-left: 5px;"/>
  <img class="vcheckImage" src="#validImage" alt="" var="image" width="100" height="40" />
  <div class="ctrl" style="width: 80px;display: inline-block;margin-left: 5px;">
    <div class="audio" var="audio" choice="audio"></div>
    <a href="#" class="reloadVcheck" var="reload" title="Unreadable image? Click to try a new image.">Reload</a>
  </div>
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }

}



/**
 *
 *
 * @package Form\Field
 */
class Capcha extends \Form\Event\Hidden
{

    /**
     * @var \Form\Field\Captcha\Adapter
     */
    protected $adapter = null;

    /**
     * __construct
     *
     * @param \Form\Field\Captcha\Adapter $adapter
     */
    public function __construct($adapter)
    {
        parent::__construct();
        $this->adapter = $adapter;
    }

    /**
     * Update
     *
     * @param Form $obs
     */
    public function update($obs)
    {
        $field = $this->adapter->getField();

        if (!$this->adapter->validateInput($field->getValue())) {
            $field->addError('Invalid verification text. Try Again!');
            return;
        }
    }


}

/**
 *
 *
 * @package Form\Field
 */
class CapchaEnd extends \Form\Event\Hidden
{

    /**
     * @var \Form\Field\Captcha\Adapter
     */
    protected $adapter = null;

    /**
     * __construct
     *
     * @param \Form\Field\Captcha\Adapter $adapter
     */
    public function __construct($adapter)
    {
        parent::__construct();
        $this->adapter = $adapter;
    }

    /**
     * Update
     *
     * @param Form $obs
     */
    public function update($obs)
    {
        if ($obs->hasErrors()) {
            return;
        }
        $this->adapter->reset();
    }

}
