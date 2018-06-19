<?php
namespace Tk\Form\Field\Option;

use Tk\Form\Field\OptGroup;
use Tk\Form\Field\Option;

/**
 * Use this iterator to create an options list from an array.
 *
 * <?php
 *   $iterator = new OptgroupIterator(array);
 * ?>
 *
 * Each sub array should contain the following structure:
 *
 *   array (
 *     'Optgroup Name 1' => array(
 *       'Name 1'  => 'Value 1',
 *       'Name 2'  => 'Value 2',
 *       ...
 *     ),
 *     'Optgroup Name 2' => array(
 *       'Name 3'  => 'Value 3',
 *       'Name 4'  => 'Value 4',
 *       ...
 *     ),
 *     ...
 *   )
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class OptGroupIterator extends ArrayIterator
{


    /**
     *
     * @param array $list
     */
    public function __construct(array $list)
    {
        parent::__construct($list);

        // create an internal structure we can iterate successfully
//        $newList = array();
//        foreach ($this->list as $k => $v) {
//            if (is_array($v)) {
//                foreach ($v as $n1 => $v1) {
//                    $newList[] = array($n1, $v1, $k);
//                }
//            } else {
//                $newList[] = array($k, $v);
//            }
//        }
//        $this->list = $newList;
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

        $option = null;
        if (is_string($key) && is_array($arr)) {
            $option = OptGroup::create($key);
            foreach ($arr as $k => $v) {
                $option->append(Option::create($k, $v));
            }
        } else {
            // TODO: Check on this one
            $option = Option::create($key, $arr);
        }
        return $option;
    }

}