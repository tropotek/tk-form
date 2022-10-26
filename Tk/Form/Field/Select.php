<?php
namespace Tk\Form\Field;


use Tk\CallbackCollection;
use Tk\Db\Mapper\ModelIface;
use Tk\Db\Mapper\Result;
use Tk\Form\Exception;
use Tk\Form\Field\Option\ArrayIterator;

/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class Select extends FieldInterface
{
    use OptionList;


    protected CallbackCollection $onShowOption;

    /**
     * Enable strict type checking for null, '', 0, false, etc
     * @todo Check if this is needed or maybe we always do strict checking???
     */
    protected bool $strict = false;


    public function __construct(string $name, array|Result|ArrayIterator $optionIterator = null)
    {
        $this->onShowOption = CallbackCollection::create();
        parent::__construct($name, self::TYPE_SELECT);

        if ($optionIterator instanceof Result) {
            $optionIterator = new Option\ArrayObjectIterator($optionIterator);
        } elseif (is_array($optionIterator)) {
            $curr = current($optionIterator);
            if (is_array($curr)) {
                $optionIterator = new Option\ArrayArrayIterator($optionIterator);
            } elseif ($curr instanceof ModelIface) {
                $optionIterator = new Option\ArrayObjectIterator($optionIterator);
            } else {
                $optionIterator = new Option\ArrayIterator($optionIterator);
            }
        }

        if ($optionIterator) {
            $this->appendOptionIterator($optionIterator);
        } else {
            throw new Exception('Invalid optionIterator.');
        }
    }

    public static function createSelect(string $name, array|Result|ArrayIterator $optionIterator = null): static
    {
        return new static($name, $optionIterator);
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

    public function load(array $values): static
    {
        if ($this->getForm()->isSubmitted() && !array_key_exists($this->getName(), $values)) {
            $this->setValue(null);
            if ($this->isMultiple()) {
                $this->setValue(array());
            }
        }
        parent::load($values);
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

    /**
     * Compare a value and see if it is selected.
     */
    public function isSelected(string $val = ''): bool
    {
        $value = $this->getValue();
        // NOTE: Ensure that null, '' and false are all separate and selectable as needed...
        if ($val !== null) {
//            if (is_array($value)) {
//                if (in_array($val, $value))
//                    return true;
//            } else {
                if ($this->isStrict()) {
                    //$val = (string)$val;
                    if ($value === $val)
                        return true;
                } else {
                    if ($value == $val)
                        return true;
                }
//            }
        }
        return false;
    }

}