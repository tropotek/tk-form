<?php
namespace Tk\Form\Action;

use Tk\CallbackCollection;
use Tk\Form\Field;
use Tk\Uri;

abstract class ActionInterface extends Field\FieldInterface
{
    protected CallbackCollection $callbackList;

    protected ?Uri $redirect = null;


    public function __construct(string $name, string $type = 'button', callable $callback = null)
    {
        $this->callbackList = CallbackCollection::create();
        parent::__construct($name, $type);
        $this->setValue($name);
        if ($callback) {
            $this->appendCallback($callback);
        }
    }

    /**
     * Execute this events callback methods/functions
     */
    public function execute(array $values = []): static
    {
        $this->getCallbackList()->execute($this->getForm(), $this);
        if ($this->getRedirect()) {
            if (!$this->getForm()->hasErrors()) {
                $this->getForm()->clearCsrf();
            }
            Uri::create($this->getRedirect())->redirect();
        }
        return $this;
    }


    public function getCallbackList(): CallbackCollection
    {
        return $this->callbackList;
    }

    /**
     * Add a callback to the start of the event queue that will be triggered onSubmit
     * function (\Tk\Form $form, \Tk\Form\Event\Iface $event) {}
     */
    public function prependCallback(callable $callback, int $priority = CallbackCollection::DEFAULT_PRIORITY): static
    {
        $this->getCallbackList()->prepend($callback, $priority);
        return $this;
    }

    /**
     * Add a callback to the end of the event queue that will be triggered onSubmit
     * function (\Tk\Form $form, \Tk\Form\Event\Iface $event) {}
     */
    public function appendCallback(callable $callback, int $priority = CallbackCollection::DEFAULT_PRIORITY): static
    {
        $this->getCallbackList()->append($callback, $priority);
        return $this;
    }

    public function getRedirect(): ?Uri
    {
        return $this->redirect;
    }

    public function setRedirect(?Uri $redirect): static
    {
        $this->redirect = $redirect;
        return $this;
    }

}