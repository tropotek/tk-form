<?php
namespace Tk\Form\Field;

use Dom\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tk\FileUtil;
use Tk\Form;

class File extends Input
{

    /**
     * The max size for this file upload in bytes
     */
    protected int $maxBytes = 0;

    /**
     * @var array|UploadedFile[]
     */
    protected array $files = [];


    public function __construct(string $name)
    {
        parent::__construct($name, self::TYPE_FILE);
        $this->maxBytes = min( \Tk\FileUtil::string2Bytes(ini_get('upload_max_filesize')),
            \Tk\FileUtil::string2Bytes(ini_get('post_max_size')) );
    }

    /**
     * The value in a string/array format that can be rendered to the template
     * Recommended that values be PHP native types not objects, use the data mapper for complex typess
     */
    public function getValue(): mixed
    {
        $default = null;
        if ($this->isMultiple()) $default = [];
        return $this->getRequest()->files->get($this->getName(), $default);
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
     *
     * Note: This is just a basic file move function, develop your own for more control.
     *
     * Returns an array of file path locations
     */
    public function move(string $path): array
    {
        $files = [];
        try {
            FileUtil::mkdir($path);
            foreach ($this->getValue() as $uploadedFile) {
                $uploadedFile->move($path, $uploadedFile->getClientOriginalName());
                $files[] = $path.'/'.$uploadedFile->getClientOriginalName();
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

        $template->insertHtml('file-notes', 'Max File Size: <b>' . \Tk\FileUtil::bytes2String($this->maxBytes, 0) . '</b><br/>');

        $this->decorate($template);

        return $template;
    }
}