<?php
namespace Tk\Form\Field;

class Html extends FieldInterface
{

    public function __construct(string $name, string $html = '')
    {
        parent::__construct($name, self::TYPE_HTML);
        parent::setValue($html);
        $this->setReadonly();
    }

    public function setValue(mixed $value): static
    {
        return $this;
    }

}