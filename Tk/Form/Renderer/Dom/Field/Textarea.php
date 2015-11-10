<?php
namespace Tk\Form\Renderer\Dom\Field;

use Tk\Form\Field;

/**
 * Class Text
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Textarea extends Iface
{


    /**
     * The default element template
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<XHTML
<textarea var="element" class="form-control"></textarea>
XHTML;
        return \Dom\Loader::load($xhtml);
    }

}