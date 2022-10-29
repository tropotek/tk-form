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
        $template = parent::show();

        $template->setText('element', $this->getValue());

        return $template;
    }

}