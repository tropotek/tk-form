<?php
namespace Tk\Form\Field\Option;

use Tk\Form\Field\Option;

/**
 * Use this iterator to create an options list from an array.
 *
 * <?php
 *   $iterator = new ArrayArrayIterator(array( array('-- Select --', ''), array('Admin', 'admin', true, 'label'), array('Moderator', 'moderator', false, 'label'), array('User', 'user')) );
 * ?>
 *
 * Each sub array should contain the following structure:
 *
 *   array (
 *     0 => 'Text',      // Option Text
 *     1 => 'Value',     // Option value (optional)
 *     2 => false,       // Option Disabled value (optional)
 *     3 => 'label'      // Option Label (optional)
 *   )
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class ArrayArrayIterator extends ArrayIterator
{

    /**
     *
     * @param array $list
     */
    public function __construct(array $list)
    {
        parent::__construct($list);
    }

    /**
     *
     * @param array $list
     * @return ArrayArrayIterator
     */
    static function create(array $list)
    {
        return new self($list);
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
        $arr = $this->list[$key];
        $text = '';
        if (isset($arr[0])) {
            $text = $arr[0];
        }
        $val = '';
        if (isset($arr[1])) {
            $val = $arr[1];
        }
        $disable = false;
        if (isset($arr[2])) {
            $disable = $arr[2];
        }
        $label = '';
        if (isset($arr[3])) {
            $label = $arr[3];
        }
        return new Option($text, $val, $disable, $label);
    }

}