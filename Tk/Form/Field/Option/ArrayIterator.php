<?php
namespace Tk\Form\Field\Option;

use Tk\Form\Field\Option;

/**
 * Use this iterator to create an options list from an array.
 *
 * <?php
 *   // supplied array('name1' => 'value1', 'name2' => 'value2', ...);
 *   $iterator = new ArrayIterator(array('-- Select --' => '', 'Admin' => 'admin', 'Moderator' => 'moderator', 'User' => 'user'));
 * ?>
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class ArrayIterator implements \Iterator, \Countable
{

    /**
     * @var int
     */
    protected $idx = 0;

    /**
     * @var array
     */
    protected $list = array();


    /**
     *
     * @param array $list
     */
    public function __construct($list)
    {
        if (key($list) == 0 && !is_object(current($list))) {
            $l = array();
            foreach($list as $k => $v) {
                $l[$k] = $v;
            }
            $list = $l;
        }
        $this->list = $list;
    }

    /**
     * @param $list
     * @return static
     */
    static function create($list)
    {
        return new static($list);
    }

    /**
     * Return the current element
     *
     * @see http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        $key = $this->getKey($this->idx);
        $el = $this->list[$key];
        return new Option($key, $el);
    }

    /**
     * getKey
     *
     * @param string $i
     * @return mixed
     */
    protected function getKey($i)
    {
        $keys = array_keys($this->list);
        if (isset($keys[$i]))
            return $keys[$i];
        return $i;
    }

    /**
     * Return the key of the current element
     *
     * @see http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->idx;
    }

    /**
     * Move forward to next element
     *
     * @see http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->idx++;
    }

    /**
     * Checks if current position is valid
     *
     * @see http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return ($this->idx < $this->count());
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @see http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->idx = 0;
    }

    /**
     * Count elements of an object
     *
     * @see http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->list);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $l = array();
        foreach ($this as $k => $v) {
            $l[$k] = $v;
        }
        return $l;
    }
}