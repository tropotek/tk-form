<?php

namespace Tk\Form\Renderer\Dom\Field;

use Dom\Template;
use Tk\Form\Renderer\Dom\FieldRendererInterface;

class File extends FieldRendererInterface
{

    function show(): ?Template
    {
        $template = $this->getTemplate();

        $field = $this->getField();
        if ($field instanceof \Tk\Form\Field\File) {
            $this->getField()->setAttr('data-maxsize', strval($field->getMaxBytes()));

            if ($field->getViewUrl()) {
                $template->setAttr('view', 'href', $field->getViewUrl());
                $template->setAttr('view', 'title', 'View: ' . $field->getViewUrl()->basename());
                $template->setVisible('view');
            }
            if ($field->getDeleteUrl()) {
                $template->setAttr('delete', 'href', $field->getDeleteUrl());
                $template->setVisible('delete');
            }
        }

        $this->decorate();

        $preNotes = '';
        if ($field instanceof \Tk\Form\Field\File) {
            $preNotes = sprintf('Max File Size: <b>%s</b><br/>', \Tk\FileUtil::bytes2String($field->getMaxBytes(), 0));
        }
        $notes = $template->getVar('notes')->nodeValue;
        $template->setHtml('notes', $preNotes . $notes);

        return $template;
    }

}