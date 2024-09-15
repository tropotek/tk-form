<?php
namespace Tk\Form\Field;

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
        $this->maxBytes = min(
            \Tk\FileUtil::string2Bytes(ini_get('upload_max_filesize')),
            \Tk\FileUtil::string2Bytes(ini_get('post_max_size'))
        );
    }

    public function hasFile(): bool
    {
        return (count($this->getUploads()) > 0);
    }

    /**
     * returns an object or an array depending on the uploaded files
     */
    public function getUploaded(): ?array
    {
        return $_FILES[$this->getName()] ?? null;
    }

    /**
     * Always returns an array of uploads
     */
    public function getUploads(): array
    {
        $up = $_FILES[$this->getName()] ?? [];
        if (isset($up['name'])) return [$up];
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
     * Returns an array of file path locations or a single path when not multiple
     *
     * Array[1] (
     *   [file] => Array[6] (
     *     [name] => 'filename.csv'
     *     [full_path] => 'filename.csv'
     *     [type] => 'text/csv'
     *     [tmp_name] => '/tmp/php0DLsG5'
     *     [error] => 0
     *     [size] => 29221
     *   )
     * )
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
                foreach ($this->getUploaded() as $file) {
                    move_uploaded_file($file['tmp_name'], "$path/{$file['name']}");
                    $files[] = $path . '/' . $file['name'];
                }
            } else {
                $files = '';
                $file = $this->getUploaded();
                if (empty($filename)) $filename = $file['name'];
                move_uploaded_file($file['tmp_name'], "$path/$filename");
            }
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
        }
        return $files;
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

    public function getMaxBytes(): int
    {
        return $this->maxBytes;
    }

}