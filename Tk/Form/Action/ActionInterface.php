<?php
namespace Tk\Form\Action;

use Tk\CallbackCollection;
use Tk\Form;
use Tk\Form\Field;
use Tk\Uri;

abstract class ActionInterface extends Field\FieldInterface
{
    protected CallbackCollection $callbackList;

    protected ?Uri $redirect = null;


    public function __construct(string $name, $type = 'button', callable $callback = null)
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

    /**
     * @todo: See if there is a better way to determin if a button is exiting a page or reloading the page.
     *        discover if this action is meant to be just a cancel action and
     *        not submit the form, maybe we need a flag like canSubmit or similar?
     */
    public function isExit(): bool
    {
        return str_ends_with($this->getValue(), '-exit');
    }

//    public function getValue(): string
//    {
//        return $this->getName();
//    }

}