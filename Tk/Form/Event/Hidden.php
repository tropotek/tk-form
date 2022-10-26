<?php
namespace Tk\Form\Event;

/**
 *
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Hidden extends FieldInterface
{

    /**
     * @param string $name
     * @param null|callable $callback
     * @param null|\Tk\Uri $redirect
     * @return static
     * @throws \Tk\Form\Exception
     */
    public static function create($name, $callback = null, $redirect = null)
    {
        return new static($name, $callback, $redirect);
    }

    /**
     * @return \Dom\Renderer\Renderer|\Dom\Template|null|string
     */
    public function show()
    {
        $t = $this->getTemplate();

        if ($t->isParsed()) return '';
        if (!$t->keyExists('var', 'element')) {
            return '';
        }
        $t->setAttr('element', 'name', $this->getEventName());
        $t->setAttr('element', 'value', $this->getEventName());
        $t->setAttr('element', $this->getAttrList());

        return $t;
    }

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<input type="hidden" var="element" />
HTML;
        return \Dom\Loader::load($xhtml);
    }

}