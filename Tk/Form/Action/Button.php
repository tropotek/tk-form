<?php
namespace Tk\Form\Action;


/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class Button extends Submit
{

    public function __construct(string $name, $callback = null)
    {
        parent::__construct($name, $callback);
        $this->setType(self::TYPE_BUTTON);
    }

}