<?php
namespace Tk\Form\Field;

use Tk\Ui\Attributes;
use Tk\Ui\Css;

class InputButton extends Input
{
    /**
     * This value can an HTML string if wanting to include icons
     */
    protected string $btnText = '';

    protected Attributes $btnAttr;

    protected Css $btnCss;

    public function __construct(string $name, string $btnText = '')
    {
        parent::__construct($name, 'input-button');
        $this->btnAttr = new Attributes();
        $this->btnCss = new Css();
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

    public function getBtnCss(): Css
    {
        return $this->btnCss;
    }

    public function addBtnCss(string $css): static
    {
        $this->btnCss->addCss($css);
        return $this;
    }

    public function setBtnAttr(array|string $name, string $value = null): static
    {
        $this->btnAttr->setAttr($name, $value);
        return $this;
    }
}