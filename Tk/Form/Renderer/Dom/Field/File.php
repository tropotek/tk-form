<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Template;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class File extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        // Render Element
        $this->getField()->setAttr('data-maxsize', $this->getField()->getMaxBytes());

        if ($this->getField()->getViewUrl()) {
            $template->setAttr('view', 'href', $this->getField()->getViewUrl());
            $template->setAttr('view', 'title', 'View: ' . $this->getField()->getViewUrl()->basename());
            $template->setVisible('view');
        }
        if ($this->getField()->getDeleteUrl()) {
            $template->setAttr('delete', 'href', $this->getField()->getDeleteUrl());
            $template->setVisible('delete');
        }


        $this->decorate();

        $preNotes = sprintf('Max File Size: <b>%s</b><br/>', \Tk\FileUtil::bytes2String($this->getField()->getMaxBytes(), 0));
        $notes = $template->getVar('notes')->nodeValue;
        $template->setHtml('notes', $preNotes . $notes);

        return $template;
    }
    
}