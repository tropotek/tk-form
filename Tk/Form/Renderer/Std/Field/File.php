<?php

namespace Tk\Form\Renderer\Std\Field;

use Tk\Form\Renderer\Std\FieldRendererInterface;

class File extends FieldRendererInterface
{

    function show(array $data = []): string
    {
        /** @var \Tk\Form\Field\File $field */
        $field = $this->getField();

        // Render Element
        $field->setAttr('data-maxsize', strval($field->getMaxBytes()));

        if ($field->getViewUrl()) {
            $data['viewBlock'][] = [
                'viewUrl' => '$this->getField()->getViewUrl()',
                'viewAttrs' => 'title="'.'View: ' . $field->getViewUrl()->basename() . '"',
            ];
        }
        if ($field->getDeleteUrl()) {
            $data['deleteBlock'][] = [
                'deleteUrl' => $field->getDeleteUrl(),
                'deleteAttrs' => '',
            ];
        }

        $data = $this->decorate($data);
        $data['html'] = $field->getValue();

        $preNotes = sprintf('Max File Size: <b>%s</b><br/>', \Tk\FileUtil::bytes2String($field->getMaxBytes(), 0));
        if (isset($data['notes'])) {
            $data['notes'] = $preNotes . ' ' . $data['notes'];
        } else {
            $data['notes'] = $preNotes;
        }

        $data['inputGroupAttrs'] = '';
        $data['inputGroupCss'] = '';
        if ($field->hasError()) {
            $data['inputGroupCss'] = $this->getFormRenderer()->getParam('error-css');
        }

        return $this->getTemplate()->parse($data);
    }

}