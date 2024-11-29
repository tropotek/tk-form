<?php
namespace Tk\Form\Field;

class InputGroup extends Input
{

    protected string $preText  = '';
    protected string $postText = '';


    public function __construct(string $name, string $preText = '', string $postText = '')
    {
        parent::__construct($name, 'input-group');
        $this->preText  = $preText;
        $this->postText = $postText;
    }

    public function getPreText(): string
    {
        return $this->preText;
    }

    public function setPreText(string $preText): InputGroup
    {
        $this->preText = $preText;
        return $this;
    }

    public function getPostText(): string
    {
        return $this->postText;
    }

    public function setPostText(string $postText): InputGroup
    {
        $this->postText = $postText;
        return $this;
    }

}