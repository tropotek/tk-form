<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2005 Michael Mifsud
 */
namespace Form;

/**
 * An iterface for objects with a key/value,
 * useful for arrays and select boxes where a key value pair is needed.
 *
 * @package Form
 */
interface SelectInterface
{
    /**
     * Get the select option value
     * This is commonly the object's ID or index in an array
     *
     * @return string
     */
    public function getSelectValue();
    
    /**
     * Get the text to show in the select option
     *
     * @return string
     */
    public function getSelectText();

}