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
    protected $iconLeft = '';

    /**
     * @var string
     */
    protected $iconRight = '';

    /**
     * @var string
     */
    private $type = 'button';


    /**
     * __construct
     *
     * @param string $name
     * @param callable $callback
     */
    public function __construct($name, $callback = null)
    {
        parent::__construct($name, $callback);

        // Not sure this belongs in the constructor as its more of a convenience than a necessity
        if ($name == 'save') {
            $this->setType('submit');
            $this->setIcon('glyphicon glyphicon-refresh');
        } else if ($name == 'update') {
            $this->setType('submit');
            $this->setIcon('glyphicon glyphicon-arrow-left');
        }
    }

    /**
     * Set the input type value
     *
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
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
        return $this->iconLeft;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->iconLeft = $icon;
        return $this;
    }


    /**
     * @return boolean
     */
    public function isIconRight()
    {
        return ($this->iconRight != '');
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIconRight($icon)
    {
        $this->iconRight = $icon;
        return $this;
    }

    /**
     * @return string
     */
    public function getIconRight()
    {
        return $this->iconRight;
    }


    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function show()
    {
        $t = $this->getTemplate();
        
        if ($t->isParsed()) return '';

        if (!$t->keyExists('var', 'element')) {
            return '';
        }

        // Field name attribute
        $t->setAttr('element', 'type', $this->getType());
        //$t->setAttr('element', 'name', $this->getName());
        $t->setAttr('element', 'name', $this->getEventName());
        $t->setAttr('element', 'value', $this->getEventName());

        // All other attributes
        foreach($this->getAttrList() as $key => $val) {
            if ($val == '' || $val == null) {
                $val = $key;
            }
            $t->setAttr('element', $key, $val);
        }

        // Element css class names
        foreach($this->getCssList() as $v) {
            $t->addCss('element', $v);
        }

        $t->insertText('text', $this->getLabel());


        if ($this->getIconRight()) {
            $t->setChoice('iconR');
            $t->addCss('iconR', $this->getIconRight());
        }
        if ($this->getIcon()) {
            $t->setChoice('iconL');
            $t->addCss('iconL', $this->getIcon());
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
<button type="button" class="btn btn-sm btn-default btn-once" var="element"><i var="iconL" choice="iconL"></i> <span var="text">Submit</span> <i var="iconR" choice="iconR"></i></button>
HTML;
        return \Dom\Loader::load($xhtml);
    }
}