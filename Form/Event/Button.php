<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Event;

/**
 * All events created from this base buttone event use the name
 * as the hook for when to execute after a form submission.
 *
 * For example if the button name is `save` then once pressed and the page is submitted
 * the form event controller will look through the $_REQUEST parameters and if the key `save`
 * is present then this event will be executed.
 *
 * @package Form\Event
 */
abstract class Button extends \Form\Field\Iface implements Iface
{

    const TYPE_SUBMIT = 'submit';
    const TYPE_RESET  = 'reset';
    const TYPE_BUTTON = 'button';
    const TYPE_IMAGE = 'image';

    /**
     * @var string
     */
    protected $buttonType = self::TYPE_SUBMIT;

    /**
     * @var \Tk\Url
     */
    protected $imageUrl = null;

    /**
     * @var \Tk\Url
     */
    protected $url = null;


    /**
     * @var string
     */
    protected $icon = '';


    /**
     * __construct
     *
     * @param string $name
     * @param string $icon
     */
    public function __construct($name, $icon = 'fa fa-check')
    {
        $this->setName($name);
        $this->setLabel(self::makeLabel($name));
        $this->icon = $icon;
        $this->addCssClass('btn-primary');
    }

    /**
     * If an image url is set then the button type is automatically set to TYPE_IMAGE
     *
     * @param \Tk\Url $url
     * @return \Form\Event\Button
     */
    public function setImageUrl(\Tk\Url $url)
    {
        $this->setButtonType(self::TYPE_IMAGE);
        $this->imageUrl = $url;
        return $this;
    }

    /**
     * set the button type
     * One Of: TYPE_SUBMIT | TYPE_BUTTON | TYPE_RESET | TYPE_IMAGE
     *
     * @param string $type
     * @return \Form\Event\Button
     */
    public function setButtonType($type)
    {
        $this->buttonType = $type;
        return $this;
    }

    /**
     * This can be set if not wanting to use
     * the request as the returning url...
     *
     * @param \Tk\Url $url
     * @return $this
     */
    public function setRedirectUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get the redirect url
     *
     * @return \Tk\Url
     */
    public function getRedirectUrl()
    {
        return $this->url;
    }

    /**
     * Render the widget.
     *
     */
    public function show()
    {
        $t = $this->getTemplate();

        if (!$t->keyExists('var', 'element')) {
            return;
        }
        $t->setAttr('element', 'name', $this->name);
        $t->setAttr('element', 'title', $this->label);
        $t->setAttr('element', 'id', $this->getId());
        $t->setAttr('element', 'value', $this->label);
        $t->setAttr('element', 'type', $this->buttonType);
        $t->insertText('text', $this->label);
        $t->insertText('label', $this->label);

        if (!$this->enabled) {
            $t->setAttr('element', 'disabled', 'disabled');
            $t->setAttr('element', 'title', 'disabled');
        }
        if ($this->accessKey) {
            $t->setAttr('element', 'accesskey', $this->accessKey);
        }
        if ($this->tabindex > 0) {
            $t->setAttr('element', 'tabindex', $this->tabindex);
        }
        foreach ($this->cssList as $class) {
            $t->addClass('element', $class);
        }
        foreach ($this->attrList as $attr => $js) {
            $t->setAttr('element', $attr, $js);
        }

        if ($this->icon) {
            $t->setChoice('icon');
            $t->addClass('icon', $this->icon);
        }

        $js = <<<JS
jQuery(function($) {
  // Disable form edit messages
  $('#{$this->getId()}').click(function(){
      $(window).unbind('beforeunload');
  });
});
JS;
        $t->appendJs($js);
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
<button type="submit" class="btn btn-sm" var="element"><i var="icon" choice="icon"></i> <span var="text">Submit</span></button>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }


}