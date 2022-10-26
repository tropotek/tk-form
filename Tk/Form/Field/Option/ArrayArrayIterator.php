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
 * @author Tropotek <http://www.tropotek.com/>
 */
class ArrayArrayIterator extends ArrayIterator
{

    /**
     * @interface \Iterator
     */
    public function current(): mixed
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