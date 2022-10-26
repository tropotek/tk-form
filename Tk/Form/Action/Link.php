<?php
namespace Tk\Form\Action;

use Tk\Uri;

/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class Link extends Submit
{

    protected Uri $url;


    public function __construct(string $name, Uri $url, string $icon = '')
    {
        parent::__construct($name);
        $this->setIcon($icon);
    }

    public function getUrl(): Uri
    {
        return $this->url;
    }

}