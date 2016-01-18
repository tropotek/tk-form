<?php
namespace Tk\Form\Field\Option;

use Tk\Form\Field\Option;

/**
 * Use this iterator to create an option list from
 * objects. The parameters that are to be accessed in the object
 * must be declared public.
 *
 * <?php
 *   $list = new ObjectArrayIterator(\App\Db\User::getMapper()->findAll(), 'name', 'id');
 * ?>
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class ArrayObjectIterator extends ArrayIterator
{
    /**
     * @var string
     */
    protected $textParam = '';

    /**
     * @var string
     */
    protected $valueParam = '';

    /**
     * @var string
     */
    protected $disableParam = '';

    /**
     * @var string
     */
    protected $labelParam = '';


    /**
     *
     * @param array|\Tk\Db\ArrayObject $list
     * @param string|callable $textParam
     * @param string|callable $valueParam
     * @param string $disableParam
     * @param string $labelParam
     */
    public function __construct($list = array(), $textParam = 'name', $valueParam = 'id', $disableParam = '', $labelParam = '')
    {
        parent::__construct($list);

        $this->textParam = $textParam;
        $this->valueParam = $valueParam;
        $this->disableParam = $disableParam;
        $this->labelParam = $labelParam;
    }

    /**
     * @param array $list
     * @param string $textParam
     * @param string $valueParam
     * @param string $disableParam
     * @param string $labelParam
     * @return ArrayObjectIterator
     */
    static function create($list = array(), $textParam = 'name', $valueParam = 'id', $disableParam = '', $labelParam = '')
    {
        return new self($list, $textParam, $valueParam, $disableParam, $labelParam);
    }

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        $obj = $this->list[$this->getKey($this->idx)];
        $text = '';
        $value = '';
        $disabled = false;

        if ( is_callable($this->textParam) ) {
            call_user_func_array($this->textParam, array($obj));
        } else if (property_exists($obj, $this->textParam)) {
            $text = $obj->{$this->textParam};
        }
        if ( is_callable($this->valueParam) ) {
            call_user_func_array($this->valueParam, array($obj));
        } else if (property_exists($obj, $this->valueParam)) {
            $value = $obj->{$this->valueParam};
        }
        if (property_exists($obj, $this->disableParam)) {
            $disabled = $obj->{$this->disableParam};
        }
        // Create the option object from the object supplied
        return new Option($text, $value, $disabled);
    }

}