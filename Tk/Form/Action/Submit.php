<?php
namespace Tk\Form\Action;

/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class Submit extends ActionInterface
{

    const ICON_LEFT = 'left';

    const ICON_RIGHT = 'right';

    /**
     * The css value for the icon eg `fa fa-check`
     */
    protected string $icon = '';

    protected string $iconPosition = self::ICON_LEFT;


    public function __construct(string $name, $callback = null)
    {
        parent::__construct($name, self::TYPE_SUBMIT, $callback);
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon, ?string $iconPosition = null): static
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

}