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
class Hidden extends Iface
{


    /**
     * Render the field
     */
    public function show()
    {
        $t = $this->getTemplate();
        if ($t->isParsed()) return;

        $this->showElement();

        return $this;
    }

    /**
     * The default element template
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<XHTML
<input type="hidden" var="element" />
XHTML;
        return \Dom\Loader::load($xhtml);
    }

}