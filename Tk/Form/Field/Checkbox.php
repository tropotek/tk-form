<?php
namespace Tk\Form\Field;

use Tk\CallbackCollection;

class Checkbox extends Select
{
    /**
     * Add toggle switch classes to template
     */
    protected bool  $switch      = false;
    protected array $optionNotes = [];


    public function __construct(string $name, array $optionList = [], string $type = self::TYPE_CHECKBOX)
    {
        $this->onShowOption = CallbackCollection::create();

        if (!$optionList) $optionList = ['1' => ''];
        if (count($optionList) > 1) $this->setMultiple(true);
        parent::__construct($name, $optionList, $type);
    }

    public function isSwitch(): bool
    {
        return $this->switch;
    }

    public function setSwitch(bool $switch): Checkbox
    {
        $this->switch = $switch;
        return $this;
    }

    public function getOptionNotes(): array
    {
        return $this->optionNotes;
    }

    public function setOptionNotes(array $optionNotes): Checkbox
    {
        $this->optionNotes = $optionNotes;
        return $this;
    }

}