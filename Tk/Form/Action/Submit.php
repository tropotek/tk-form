<?php
namespace Tk\Form\Action;

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

    /**
     * @todo: See if there is a better way to determine if a button is exiting a page or reloading the page.
     *        discover if this action is meant to be just a cancel action and
     *        not submit the form, maybe we need a flag like canSubmit or similar?
     */
    public function isExit(): bool
    {
        return str_ends_with($this->getValue(), '-exit');
    }

}