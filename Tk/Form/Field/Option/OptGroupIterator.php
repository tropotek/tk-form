<?php
namespace Tk\Form\Field\Option;

use Tk\Form\Field\OptionGroup;
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
 */
class OptGroupIterator extends ArrayIterator
{


    /**
     * @interface \Iterator
     */
    public function current(): mixed
    {
        $key = $this->getKey($this->idx);
        $arr = $this->list[$key];

        $option = null;
        if (is_string($key) && is_array($arr)) {
            $option = OptionGroup::create($key);
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