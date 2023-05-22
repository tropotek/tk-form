<?php
namespace Tk\Form\Action;

use Dom\Template;

class Submit extends ActionInterface
{

    const ICON_LEFT = 'left';
    const ICON_RIGHT = 'right';

    /**
     * The css value for the icon eg `fa fa-check`
     */
    protected string $icon = '';

    protected string $iconPosition = self::ICON_LEFT;


    public function __construct(string $name, callable $callback = null)
    {
        parent::__construct($name, self::TYPE_SUBMIT, $callback);
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon, string $iconPosition = null): static
    {
        $this->icon = $icon;
        if ($iconPosition) $this->setIconPosition($iconPosition);
        return $this;
    }

    public function getIconPosition(): string
    {
        return $this->iconPosition;
    }

    public function setIconPosition(string $iconPosition): static
    {
        $this->iconPosition = $iconPosition;
        return $this;
    }

    function show(): ?Template
    {
        $template = $this->getTemplate();

        // Render Element
        $template->setAttr('element', 'id', $this->getId());
        if ($this->getType() != self::TYPE_LINK) {
            $template->setAttr('element', 'name', $this->getId());
            $template->setAttr('element', 'type', $this->getType());
            $template->setAttr('element', 'value', $this->getValue());
        }
        $template->setText('text', $this->getLabel());

        if ($this->getIcon()) {
            if ($this->getIconPosition() == self::ICON_LEFT) {
                $template->setVisible('icon-l');
                $template->addCss('icon-l', $this->getIcon());
            } else {
                $template->setVisible('icon-r');
                $template->addCss('icon-r', $this->getIcon());
            }
        } else {
            // this removed HTMX bug with tags in the button???
            $template->setText('element', $this->getLabel());
        }

        $this->getOnShow()?->execute($template, $this);

        $template->setAttr('element', $this->getAttrList());
        $template->addCss('element', $this->getCssList());

        return $template;
    }
}