<?php
namespace Tk\Form\Field;

use Dom\Template;

class Html extends FieldInterface
{

    public function __construct(string $name, string $html = '')
    {
        parent::__construct($name, self::TYPE_HTML);
        parent::setValue($html);
        $this->setReadonly(true);
    }

    public function setValue(mixed $value): static
    {
        return $this;
    }

    function show(): ?Template
    {
        $template = $this->getTemplate();

        // Render Element
        $this->setAttr('name', $this->getHtmlName());
        $this->setAttr('id', $this->getId());
        $template->insertHtml('element', $this->getValue());
        $this->decorate($template);

        return $template;
    }
}