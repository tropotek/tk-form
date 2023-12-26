<?php
namespace Tk\Form\Field;

use Tk\Db\Mapper\Result;
use Tk\Form\Field\Option\ArrayIterator;

class Checkbox extends Select
{

    protected array $optionNotes = [];

    /**
     * Add toggle switch classes to template
     */
    protected bool $switch = false;

    public function __construct(string $name, null|array|Result|ArrayIterator $optionIterator = null)
    {
        if (!$optionIterator) $optionIterator = [$name];
        parent::__construct($name, $optionIterator);
        if (count($optionIterator) > 1) $this->setMultiple(true);
        $this->setType(self::TYPE_CHECKBOX);
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

    protected function createIterator(array|Result|ArrayIterator $optionIterator = null, string $nameParam = 'name', string $valueParam = 'id', string $selectAttr = 'checked'): ?Option\ArrayIterator
    {
        return parent::createIterator($optionIterator, $nameParam,  $valueParam, $selectAttr);
    }

}