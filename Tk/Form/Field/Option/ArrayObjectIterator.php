<?php
namespace Tk\Form\Field\Option;

use Tk\Form\Field\Option;
use Tk\ObjectUtil;

/**
 * Use this iterator to create an option list from
 * objects. The parameters that are to be accessed in the object
 * must be declared public.
 *
 * <?php
 *   $list = new ObjectArrayIterator(\App\Db\User::getMapper()->findAll(), 'name', 'id');
 * ?>
 *
 */
class ArrayObjectIterator extends ArrayIterator
{

    protected string $textParam       = '';
    protected string $valueParam      = '';
    protected string $disableParam    = '';
    protected string $labelParam      = '';
    protected string $selectedValue   = '';
    protected string $selectedAppend  = ' (Current)';
    protected string $selectedPrepend = '';


    public function __construct(
        array $list = [],
        callable|string $textParam = 'name',
        callable|string $valueParam = 'id',
        string $selectAttr = 'selected',
        string $disableParam = '',
        string $labelParam = ''
    ) {
        parent::__construct($list, $selectAttr);

        $this->textParam = $textParam;
        $this->valueParam = $valueParam;
        $this->disableParam = $disableParam;
        $this->labelParam = $labelParam;
    }

    public function setSelectedValue(string $value): static
    {
        $this->selectedValue = $value;
        return $this;
    }

    public function setSelectedAppend(string $selectedAppend): static
    {
        $this->selectedAppend = $selectedAppend;
        return $this;
    }

    public function setSelectedPrepend(string $selectedPrepend): static
    {
        $this->selectedPrepend = $selectedPrepend;
        return $this;
    }

    /**
     * @interface \Iterator
     */
    public function current(): Option
    {
        $obj = $this->list[$this->getKey(strval($this->idx))];

        if (is_callable($this->valueParam)) {
            $value = call_user_func_array($this->valueParam, array($obj));
        } else {
            $value = ObjectUtil::getPropertyValue($obj, $this->valueParam);
        }

        $pre = $app = '';
        if ($this->selectedValue !== '' && $value == $this->selectedValue) {
            $pre = $this->selectedPrepend;
            $app = $this->selectedAppend;
        }

        if (is_callable($this->textParam)) {
            $text = call_user_func_array($this->textParam, array($obj));
        } else {
            $text = ObjectUtil::getPropertyValue($obj, $this->textParam);
            $text = sprintf('%s%s%s', $pre, $text, $app);
        }

        $option = Option::create($text, $value, $this->getSelectAttr());

        if (property_exists($obj, $this->disableParam)) {
            if ($obj->{$this->disableParam})
                $option->setDisabled(true);
        }

        // Create the option object from the object supplied
        return $option;
    }

}