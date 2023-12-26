<?php
namespace Tk\Form\Field;

use Tk\CallbackCollection;
use Tk\Db\Mapper\ModelInterface;
use Tk\Db\Mapper\Result;
use Tk\Form\Exception;
use Tk\Form\Field\Option\ArrayIterator;

class Select extends FieldInterface
{
    use OptionList;

    protected CallbackCollection $onShowOption;

    /**
     * Enable strict type checking for null, '', 0, false, etc
     */
    protected bool $strict = false;


    public function __construct(string $name, array|Result|ArrayIterator $optionIterator = null, string $nameParam = 'name', string $valueParam = 'id')
    {
        $this->onShowOption = CallbackCollection::create();
        parent::__construct($name, self::TYPE_SELECT);

        $optionIterator = $this->createIterator($optionIterator, $nameParam, $valueParam);

        if ($optionIterator) {
            $this->appendOptionIterator($optionIterator);
        } else {
            throw new Exception('Invalid optionIterator.');
        }
    }

    protected function createIterator(array|Result|ArrayIterator $optionIterator = null, string $nameParam = 'name', string $valueParam = 'id', string $selectAttr = 'selected'): ?Option\ArrayIterator
    {
        if ($optionIterator instanceof Result) {
            $optionIterator = new Option\ArrayObjectIterator($optionIterator, $nameParam, $valueParam, $selectAttr);
        } elseif (is_array($optionIterator)) {
            $curr = current($optionIterator);
            if (is_array($curr)) {
                $optionIterator = new Option\ArrayArrayIterator($optionIterator, $selectAttr);
            } elseif ($curr instanceof ModelInterface) {
                $optionIterator = new Option\ArrayObjectIterator($optionIterator, $nameParam, $valueParam, $selectAttr);
            } else {
                $optionIterator = new Option\ArrayIterator($optionIterator, $selectAttr);
            }
        }
        return $optionIterator;
    }

    public static function createSelect(string $name, array|Result|ArrayIterator $optionIterator = null): static
    {
        return new static($name, $optionIterator);
    }

    public function appendOptionIterator(Option\ArrayIterator $optionIterator): static
    {
        foreach($optionIterator as $option) {
            $this->append($option);
        }
        return $this;
    }

    public function prependOptionIterator(Option\ArrayIterator $optionIterator): static
    {
        foreach($optionIterator as $option) {
            $this->prepend($option);
        }
        return $this;
    }

    public function getOnShowOption(): CallbackCollection
    {
        return $this->onShowOption;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function setStrict(bool $strict): static
    {
        $this->strict = $strict;
        return $this;
    }

    /**
     *  function (\Dom\Template $template, \Tk\Form\Field\Option $option, $var) { }
     */
    public function addOnShowOption(callable $callable, $priority = CallbackCollection::DEFAULT_PRIORITY): static
    {
        $this->getOnShowOption()->append($callable, $priority);
        return $this;
    }

    protected function clearSelected(): static
    {
        /** @var Option $option */
        foreach ($this->getOptions() as $option) {
            $option->setSelected(false);
        }
        return $this;
    }

    /**
     * The value in a string format
     */
    public function setValue(mixed $value): static
    {
        $this->value = $value;
        if ($this->isMultiple() && empty($value)) {
            $this->value = [];
        }
        $this->clearSelected();
        /** @var Option $option */
        foreach ($this->getOptions() as $option) {
            if ($this->isMultiple()) {
                if (is_array($value) && in_array($option->getValue(), $value, $this->isStrict())) {
                    $option->setSelected();
                }
            } else {
                if ($this->isStrict()) {
                    if ($option->getValue() === $value) {
                        $option->setSelected();
                    }
                } else {
                    if ($option->getValue() == $value) {
                        $option->setSelected($this->getValue() ?: false);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * The value in a string format
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * take a single dimensional array and convert it to a list for the select
     *
     * Input example:
     *     array('test', 'twoWord', 'three_word_test', 'another test');
     * Output:
     *     array('Test' => 'test', 'Two Word' => 'twoWord', 'Three Word Test' => 'three_word_test', 'Another Test' => 'another test')
     */
    public static function arrayToSelectList(array $arr, bool $modify = true): array
    {
        //$arr = array('test', 'twoWord', 'three_word_test', 'another test');
        $new = array();
        foreach ($arr  as $v) {
            $n = $v;
            if ($modify) {
                $n = preg_replace('/[^A-Z0-9]/i', ' ', $n);
                $n = preg_replace('/[A-Z]/', ' $0', $n);
                $n = ucwords($n);
            }
            $new[$n] =  $v;
        }
        return $new;
    }
}