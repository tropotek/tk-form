<?php
namespace Tk\Form\Action;

use Dom\Template;
use Tk\Uri;

/**
 * @author Tropotek <http://www.tropotek.com/>
 */
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

    function show(): ?Template
    {
        $template = parent::show();

        $template->setAttr('element', 'href', $this->getUrl());
        //$template->replaceHtml('text', $this->getLabel());

        return $template;
    }

}