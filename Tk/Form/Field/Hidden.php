<?php
namespace Tk\Form\Field;


/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class Hidden extends Input
{

    public function __construct(string $name, string $value = '')
    {
        parent::__construct($name);
        $this->setValue($value);
        $this->setType('hidden');
    }

    public function getFieldset(): string
    {
        return '';
    }

    public function getTabGroup(): string
    {
        return '';
    }

}