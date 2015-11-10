<?php
namespace Tk\Form\Type;


/**
 * Class DateTime
 *
 *
 * @todo: Add a format parameter in the constructor so the date format can be specified
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class DateTime extends Iface
{

    /**
     * Convert the basic form submitted string field value
     * into its correct complex type.
     *
     * @param array|\stdClass $array
     * @return mixed
     */
    public function toType($array)
    {
        $text = $array[$this->getField()->getName()];

        if ($text && !preg_match('/^([0-9]{1,2})(\/|-)([0-9]{1,2})(\/|-)([0-9]{2,4})( ([0-9]{1,2}):([0-9]{1,2})(:([0-9]{1,2}))?)?$/', $text)) {
            $this->getField()->addError('Invalid date format [dd/mm/yyyy].');
            return null;
        } else if (!$text) {
            return null;
        }

        return \DateTime::createFromFormat('d/m/Y', $text);
    }

    /**
     * Convert the field's complex type into
     * a string for the required field
     *
     * @param array|\stdClass $array
     * @return string
     */
    public function toText($array)
    {
        /** @var \DateTime $date */
        $date = $array[$this->getField()->getName()];
        if ($date) {
            return $date->format('d/m/Y');
        }
        return '';
    }

}
