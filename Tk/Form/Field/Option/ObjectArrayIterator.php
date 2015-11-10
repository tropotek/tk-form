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
class ObjectArrayIterator extends ArrayIterator
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
     * @param array $list
     * @param string $textParam
     * @param string $valueParam
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

        if (property_exists($obj, $this->textParam)) {
            $text = $obj->{$this->textParam};
        }
        if (property_exists($obj, $this->valueParam)) {
            $value = $obj->{$this->valueParam};
        }
        if (property_exists($obj, $this->disableParam)) {
            $disabled = $obj->{$this->disableParam};
        }
        // Create the option object from the object supplied
        return new Option($text, $value, $disabled);
    }

}