<?php
namespace Tk\Form\Field;

use Dom\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tk\FileUtil;
use Tk\Form;
use Tk\Uri;

/**
 * A file upload field
 *
 * Note: This field UI is not designed to handle multi uploads.
 *       For multiple uploads do not user the view/delete icons
 *       you will need to develop your own UI
 */
class File extends Input
{

    /**
     * The max size for this file upload in bytes
     */
    protected int $maxBytes = 0;

    protected ?Uri $deleteUrl = null;

    protected ?Uri $viewUrl = null;


    public function __construct(string $name)
    {
        parent::__construct($name, self::TYPE_FILE);
        $this->maxBytes = min( \Tk\FileUtil::string2Bytes(ini_get('upload_max_filesize')),
            \Tk\FileUtil::string2Bytes(ini_get('post_max_size')) );
    }

    public function hasFile(): bool
    {
        return (count($this->getUploaded()) > 0);
    }

    /**
     * returns an object or an array depending on the uploaded files
     */
    public function getUploaded(): mixed
    {
        $default = null;
        if ($this->isMultiple()) $default = [];
        return $this->getRequest()->files->get($this->getName(), $default);
    }

    /**
     * Always returns an array of uploads
     *
     * @return array|UploadedFile[]
     */
    public function getUploads(): array
    {
        $up = $this->getRequest()->files->get($this->getName(), []);
        if (!is_array($up)) $up = [$up];
        return $up;
    }

    /**
     * Set the form for this element
     */
    public function setForm(Form $form): static
    {
        parent::setForm($form);
        $form->setAttr('enctype', Form::ENCTYPE_MULTIPART);
        return $this;
    }

    /**
     * Use this to move the attached files to a directory in $path
     * The file names will be what the original uploaded file name was.
     *
     * Any existing files will be overwritten.
     * Returns an array of file path locations or a single path when not mutiple
     *
     * @param string $filename (optional) Only used in single file upload mode
     */
    public function move(string $path, string $filename = ''): string|array
    {
        $files = null;
        try {
            FileUtil::mkdir($path);
            if ($this->isMultiple()) {
                $files = [];
                foreach ($this->getUploaded() as $uploadedFile) {
                    $uploadedFile->move($path, $uploadedFile->getClientOriginalName());
                    $files[] = $path . '/' . $uploadedFile->getClientOriginalName();
                }
            } else {
                $files = '';
                $uploadedFile = $this->getUploaded();
                $files = $uploadedFile->move($path, $filename ?: $uploadedFile->getClientOriginalName());
            }
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
        }
        return $files;
    }

    function show(): ?Template
    {
        $template = $this->getTemplate();

        // Render Element
        $this->setAttr('data-maxsize', $this->maxBytes);
        $this->setAttr('name', $this->getHtmlName());
        $this->setAttr('id', $this->getId());
        $this->setAttr('type', $this->getType());


        if ($this->getViewUrl()) {
            $template->setAttr('view', 'href', $this->getViewUrl());
            $template->setAttr('view', 'title', 'View: ' . $this->getViewUrl()->basename());
            $template->setVisible('view');
        }
        if ($this->getDeleteUrl()) {
            $template->setAttr('delete', 'href', $this->getDeleteUrl());
            $template->setAttr('delete', 'title', 'Delete');
            $template->setVisible('delete');
        }


        $this->decorate($template);

        $preNotes = sprintf('Max File Size: <b>%s</b><br/>', \Tk\FileUtil::bytes2String($this->maxBytes, 0));
        $notes = $template->getVar('notes')->nodeValue;
        $template->insertHtml('notes', $preNotes . $notes);

        return $template;
    }

    public function getDeleteUrl(): ?Uri
    {
        return $this->deleteUrl;
    }

    public function setDeleteUrl(string|Uri $deleteUrl): static
    {
        $this->deleteUrl = Uri::create($deleteUrl);
        return $this;
    }

    public function getViewUrl(): ?Uri
    {
        return $this->viewUrl;
    }

    public function setViewUrl(string|Uri $viewUrl): static
    {
        $this->viewUrl = Uri::create($viewUrl);
        return $this;
    }

}