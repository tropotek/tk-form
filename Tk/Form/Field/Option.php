<?php
namespace Tk\Form\Field;

use Dom\Renderer\Traits\AttributesTrait;
use Dom\Renderer\Traits\CssTrait;

/**
 * @author tropotek <http://www.tropotek.com/>
 * @see http://www.w3schools.com/tags/tag_option.asp
 */
class Option
{
    use AttributesTrait;
    use CssTrait;

    protected string $name = '';

    protected string $value = '';


    public function __construct(string $name, string $value = '')
    {
        $this->name = $name;
        $this->value = $value;
    }

    static function create(string $name, string $value = ''): static
    {
        return new static($name, $value);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isDisabled(): bool
    {
        return $this->hasAttr('disabled');
    }

    public function setDisabled(bool $b = true): static
    {
        if ($b) {
            $this->setAttr('disabled', 'disabled');
        } else {
            $this->removeAttr('disabled');
        }
        return $this;
    }

}