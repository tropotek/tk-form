<?php
namespace Tk\Form\Action;

use Tk\Uri;

class Link extends Submit
{

    protected Uri $url;


    public function __construct(string $name, Uri $url, string $icon = '')
    {
        $this->url = $url;
        parent::__construct($name);
        $this->setType(self::TYPE_LINK);
        if ($icon) {
            $this->setIcon($icon);
        }
    }

    public function getUrl(): Uri
    {
        return $this->url;
    }

}