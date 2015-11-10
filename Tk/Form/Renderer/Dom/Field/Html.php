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
class Html extends Iface
{

    protected $html = '';


    /**
     * __construct
     *
     * @param Field\Iface $field
     */
    public function __construct(Field\Iface $field, $html)
    {
        $this->html = $html;
        $this->field = $field;
    }

    /**
     * Render the field and return the template or html string
     */
    public function showElement()
    {
        $t = $this->getTemplate();
        if (!$t->keyExists('var', 'element')) {
            return;
        }
        $ret = parent::showElement();

        $t->appendHtml('element', $this->html);

        return $ret;
    }

    /**
     * The default element template
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<XHTML
<div var="element"></div>
XHTML;
        return \Dom\Loader::load($xhtml);
    }

}