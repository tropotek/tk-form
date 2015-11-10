<?php
namespace Tk\Form\Renderer\Dom\Field;

use Tk\Form\Field;

/**
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class File extends Iface
{


    /**
     * Render the field
     */
    public function show()
    {
        $notes = '<i>Max File Size: ' . Field\File::bytes2String($this->getField()->getMaxFileSize(), 0).'</i>';
        if ($this->getField()->getNotes()) {
            $notes .= '<br/>';
            $notes .= $this->getField()->getNotes();
        }

        $this->getField()->setNotes($notes);

        return parent::show();
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

        if ($this->getField()->isArray()) {
            $t->setAttr('element', 'multiple', 'multiple');
        }

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
<input type="file" var="element" />
XHTML;
        return \Dom\Loader::load($xhtml);
    }

}