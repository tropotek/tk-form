<?php
namespace Tk\Form\Field;

use Tk\Ui\Attributes;

class InputButton extends Input
{
    /**
     * This value can an HTML string if wanting to include icons
     */
    protected string $btnText = '';

    protected Attributes $btnAttr;

    public function __construct(string $name, string $btnText = '')
    {
        parent::__construct($name, 'input-button');
        $this->btnAttr = new Attributes();
        $this->btnText = $btnText;
        $this->setAttr('target', '_blank');
    }

    public function getBtnText(): string
    {
        return $this->btnText;
    }

    public function getBtnAttr(): Attributes
    {
        return $this->btnAttr;
    }

    public function addBtnCss(string $css): static
    {
        $this->btnAttr->addCss($css);
        return $this;
    }

    public function setBtnAttr(array|string $name, string $value = null): static
    {
        $this->btnAttr->setAttr($name, $value);
        return $this;
    }

    /**
     * @deprecated use getBtnAttr()
     */
    public function getBtnCss(): Attributes
    {
        return $this->btnAttr;
    }
}