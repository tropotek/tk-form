<?php
namespace Tk\Form\Field;


use Dom\Template;

/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class Hidden extends FieldInterface
{

    public function __construct(string $name, string $value = '')
    {
        parent::__construct($name, self::TYPE_HIDDEN);
        $this->setValue($value);
    }

    function show(): ?Template
    {
        $template = $this->getTemplate();

        $this->setAttr('name', $this->getHtmlName());
        $this->setAttr('id', $this->getId());
        $this->setAttr('type', $this->getType());
        if (!is_array($this->getValue()) && !is_object($this->getValue())) {
            $this->setAttr('value', $this->getValue());
        }

        if ($this->getOnShow()) {
            $this->getOnShow()->execute($template, $this);
        }

        // Add any attributes
        $template->setAttr('element', $this->getAttrList());

        return $template;
    }

}