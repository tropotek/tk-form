<?php
namespace Tk\Form\Event;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Button extends Iface
{

    /**
     * @var string
     */
    protected $icon = '';

    /**
     * @var string
     */
    private $type = 'submit';


    /**
     * __construct
     *
     * @param string $name
     * @param callable $callback
     */
    public function __construct($name, $callback = null, $icon = '')
    {
        parent::__construct($name, $callback);
        if (!$icon) {
            if ($name == 'save') {
                $icon = 'glyphicon glyphicon-refresh';
            } else if ($name == 'update') {
                $icon = 'glyphicon glyphicon-arrow-left';
            }
        }
        $this->setIcon($icon);
    }

    /**
     * Set the input type value
     *
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        vdd($type);
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        $t = $this->getTemplate();
        
        if ($t->isParsed()) return '';

        if (!$t->keyExists('var', 'element')) {
            return '';
        }

        // Field name attribute
        $t->setAttr('element', 'type', $this->getType());
        $t->setAttr('element', 'name', $this->getName());

        // All other attributes
        foreach($this->getAttrList() as $key => $val) {
            if ($val == '' || $val == null) {
                $val = $key;
            }
            $t->setAttr('element', $key, $val);
        }

        // Element css class names
        foreach($this->getCssClassList() as $v) {
            $t->addClass('element', $v);
        }

        $t->insertText('text', $this->getLabel());
        if ($this->getIcon()) {
            $t->setChoice('icon');
            $t->addClass('icon', $this->getIcon());
        }
        
        return $t;
    }

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<button type="submit" class="btn btn-sm btn-default" var="element"><i var="icon" choice="icon"></i> <span var="text">Submit</span></button>
HTML;
        return \Dom\Loader::load($xhtml);
    }
}