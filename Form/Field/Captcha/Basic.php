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
class Basic extends Adapter
{

    const SID = 'captcha_';


    /**
     * Get the capture image URL
     *
     * @return \Tk\Url
     */
    public function getImageUrl()
    {
        /* TODO: We need a solution that removes the image.php from the lib path to a public path */
        return \Tk\Url::create(dirname($this->getClassUrl()) . '/Basic/image.php')->set('_disableLog')->set('id', $this->getInstanceId());
    }

    /**
     * Get the audio html if available.
     * NOTE: This could be substituted for other
     * alternate code methods also.....
     *
     * @return strings
     */
    public function getAudioHtml()
    {
        return '';
    }

    /**
     * Get the session key for the capcha data
     *
     * @return string
     */
    public function getSessionId()
    {
        return self::SID . $this->getInstanceId();
    }

    /**
     *
     *
     * @param string $input
     * @return bool
     */
    public function validateInput($input)
    {
        if ($this->getSession()->get($this->getSessionId()) == $input) {
            return true;
        }
        return false;
    }

    /**
     * Reset the capture session as it has been validated.
     *
     * @return bool
     */
    public function reset()
    {
        if ($this->getSession()->exists($this->getSessionId())) {
            $this->getSession()->delete($this->getSessionId());
        }
    }


}
