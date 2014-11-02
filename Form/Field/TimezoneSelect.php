<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 *
 *
 * @package Form\Field
 */
class TimezoneSelect extends Select
{

    public function __construct($name)
    {
        $timezones = \DateTimeZone::listAbbreviations();
        $zonelist = array();
        foreach( $timezones as $key => $zones ) {
            foreach( $zones as $id => $zone ) {
                if (!$zone['timezone_id']) continue;
                $zonelist[$zone['timezone_id']] = array($zone['timezone_id'], $zone['timezone_id']);
            }
        }
        ksort($zonelist);
        parent::__construct($name, $zonelist);
    }



}
