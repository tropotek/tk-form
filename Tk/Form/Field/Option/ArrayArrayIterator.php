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
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
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
     * Return the current element
     *
     * @see http://php.net/manual/en/iterator.current.php
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
        $option = Option::create($text, $val);

        if (!empty($arr[2])) {
            $option->setAttr('disabled', 'disabled');
        }
        if (!empty($arr[3])) {
            $option->setAttr('label', $arr[3]);
        }

        return $option;
    }

}