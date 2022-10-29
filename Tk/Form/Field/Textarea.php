<?php
namespace Tk\Form\Field;


use Dom\Template;

/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class Textarea extends Input
{

    public function __construct(string $name)
    {
        parent::__construct($name, self::TYPE_TEXTAREA);
    }

    function show(): ?Template
    {
        $template = $this->getTemplate();

        // Render Element
        $this->setAttr('name', $this->getHtmlName());
        $this->setAttr('id', $this->getId());

        if (!is_array($this->getValue()) && !is_object($this->getValue())) {
            $template->setText('element', $this->getValue());
        }

        $this->decorate($template);

        return $template;
    }

}