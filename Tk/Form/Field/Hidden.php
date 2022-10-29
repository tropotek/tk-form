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

        $template->setAttr('element', 'name', $this->getName());
        $template->setAttr('element', 'type', $this->getType());
        $template->setAttr('element', 'value', $this->getValue());

        if ($this->getOnShow()) {
            $this->getOnShow()->execute($template, $this);
        }

        // Add any attributes
        $template->setAttr('element', $this->getAttrList());

        return $template;
    }

}