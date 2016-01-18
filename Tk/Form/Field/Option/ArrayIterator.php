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
 * @link http://www.tropotek.com/
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
    public function __construct(array $list)
    {
        if (key($list) == 0) {
            $l = array();
            foreach($list as $v) {
                $l[$v] = $v;
            }
            $list = $l;
        }
        $this->list = $list;
    }

    /**
     * 
     * @param array $list
     * @return ArrayIterator
     */
    static function create(array $list)
    {
        return new self($list);
    }
    

    /**
     * getKey
     *
     * @param $i
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
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
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
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        //return $this->getKey($this->idx);
        return $this->idx;
    }

    /**
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
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
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return ($this->idx < $this->count());
        //return isset($this->list[$this->getKey($this->idx)]);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
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
     * @link http://php.net/manual/en/countable.count.php
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

}