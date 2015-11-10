<?php
namespace Tk\Form\Renderer\Dom\Field;


/**
 * Class Text
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Password extends Iface
{

    /**
     * The default element template
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<XHTML
<input type="password" var="element" class="form-control" autocomplete="off" />
XHTML;
        return \Dom\Loader::load($xhtml);
    }

}