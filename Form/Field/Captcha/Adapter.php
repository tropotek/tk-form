<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field\Captcha;

/**
 * 
 *
 * @package Form\Field\Captcha
 */
abstract class Adapter extends \Tk\Object
{
    /**
     * @var \Form\Field\Captcha
     */
    protected $field = null;
    
    
    
    
    
    
    /**
     * Set the parent field object.
     * 
     * @param \Form\Field\Captcha $field
     */
    public function setField(\Form\Field\Captcha $field)
    {
        $this->field = $field;
    }
    
    /**
     * Get the captcha field
     * 
     * @return \Form\Field\Captcha
     */
    public function getField()
    {
        return $this->field;
    }
    
    
    
    
    /**
     * Get the capture image URL
     * 
     * @return \Tk\Url
     */
    abstract function getImageUrl();
    
    /**
     * Get the audio html if available.
     * NOTE: This could be substituted for other 
     * alternate code methods also.....
     * 
     * @return strings 
     */
    abstract function getAudioHtml();
    
    /**
     * Get the session key for the capcha data
     * 
     * @return string 
     */
    abstract function getSessionId();
    
    /**
     * 
     * 
     * @param string $input
     * @return bool
     */
    abstract function validateInput($input);
    
    /**
     * Reset the capture session as it has been validated.
     * 
     * @return bool
     */
    abstract function reset();
    
}
