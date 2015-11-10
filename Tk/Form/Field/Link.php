<?php
namespace Tk\Form\Field;

use Tk\Form\Type;

/**
 * Class Link
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Link extends Button
{

    /**
     * @var string|\Tk\Url
     */
    protected $url = null;

    /**
     * __construct
     *
     * @param string $name
     * @param string $url
     * @param callable $callback
     */
    public function __construct($name, $url, $callback = null)
    {
        if (!$url) {
            $url = \Tk\Url::create()->set($name, $name);
        }
        $this->url = $url;
        parent::__construct($name, $callback);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

}