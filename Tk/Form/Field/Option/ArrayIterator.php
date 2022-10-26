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
 * @author Tropotek <http://www.tropotek.com/>
 */
class ArrayIterator implements \Iterator, \Countable
{

    protected int $idx = 0;

    protected array $list = [];


    public function __construct(array $list)
    {
        if (key($list) == 0 && !is_object(current($list))) {
            $list = array_combine($list, $list);
        }
        $this->list = $list;
    }

    static function create(array $list): static
    {
        return new static($list);
    }

    /**
     * @interface \Iterator
     */
    public function current(): mixed
    {
        $key = $this->getKey($this->idx);
        $el = $this->list[$key];
        return new Option($key, $el);
    }

    /**
     * @interface \Iterator
     */
    protected function getKey(string $i): mixed
    {
        $keys = array_keys($this->list);
        if (isset($keys[$i]))
            return $keys[$i];
        return $i;
    }

    /**
     * @interface \Iterator
     */
    public function key(): mixed
    {
        return $this->idx;
    }

    /**
     * @interface \Iterator
     */
    public function next(): void
    {
        $this->idx++;
    }

    /**
     * @interface \Iterator
     */
    public function valid(): bool
    {
        return ($this->idx < $this->count());
    }

    /**
     * @interface \Iterator
     */
    public function rewind(): void
    {
        $this->idx = 0;
    }

    /**
     * @interface \Countable
     */
    public function count(): int
    {
        return count($this->list);
    }

    public function toArray(): array
    {
        return $this->list;
    }
}