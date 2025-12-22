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
    const array ERROR_MSG = [
        UPLOAD_ERR_OK         => 'There is no error, the file uploaded with success.',
        UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
    ];

    /**
     * The max size for this file upload in bytes
     */
    protected int  $maxBytes  = 0;
    protected ?Uri $deleteUrl = null;
    protected ?Uri $viewUrl   = null;


    public function __construct(string $name)
    {
        parent::__construct($name, self::TYPE_FILE);
        $this->maxBytes = min(
            \Tk\FileUtil::string2Bytes(strval(ini_get('upload_max_filesize'))),
            \Tk\FileUtil::string2Bytes(strval(ini_get('post_max_size')))
        );
    }

    public function hasFile(): bool
    {
        return (count($this->getUploads()) > 0);
    }

    /**
     * returns an object or an array depending on the uploaded files
     *
     * @return array<string,mixed>|null
     */
    public function getUploaded(): ?array
    {
        return $this->getUploads()[0] ?? null;
    }

    /**
     * Always returns an array of uploads
     *
     * @return array<int,array<string,mixed>>
     */
    public function getUploads(): array
    {
        $up = [];

        if (!isset($_FILES[$this->getName()])) return $up;

        // single file returned
        if (!is_array($_FILES[$this->getName()]['name'])) {
            return [$_FILES[$this->getName()]];
        }

        // multiple files returned
        $files = self::normalize($_FILES);
        if (!count($files[$this->getName()])) return $up;

        foreach ($files[$this->getName()] as $file) {
            $up[] = $file;
        }

        return $up;
    }

    public function isValid(): bool
    {
        foreach ($this->getUploads() as $file) {
            if (($file['error'] ?? '') != UPLOAD_ERR_OK) {
                $this->setError(self::ERROR_MSG[$file['error']]);
                return false;
            }
        }
        return true;
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
    public function move(string $path, string $filename = ''): bool
    {
        FileUtil::mkdir($path);
        if ($this->isMultiple()) {
            foreach ($this->getUploaded() as $file) {
                $ok = move_uploaded_file($file['tmp_name'], "$path/{$file['name']}");
                if (!$ok) return false;
            }
        } else {
            $file = $this->getUploaded();
            if (empty($filename)) $filename = $file['name'];
            return move_uploaded_file($file['tmp_name'], "$path/$filename");
        }
        return true;
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

    /**
     * @param array<string, array<string, list<mixed>>> $_files
     * @return array<string|int, mixed>
     */
    public static function normalize(array $_files, bool $top = true): array
    {
        $files = [];
        foreach ($_files as $name => $file) {
            $subName = $top ? $file['name'] : $name;

            if (is_array($subName)) {
                foreach (array_keys($subName) as $key) {
                    $files[$name][$key] = [
                        'name'     => $file['name'][$key],
                        'type'     => $file['type'][$key],
                        'tmp_name' => $file['tmp_name'][$key],
                        'error'    => $file['error'][$key],
                        'size'     => $file['size'][$key],
                    ];
                    $files[$name] = self::normalize($files[$name], false);
                }
            } else {
                $files[$name] = $file;
            }
        }
        return $files;
    }
}