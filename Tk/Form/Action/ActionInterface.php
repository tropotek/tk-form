<?php
namespace Tk\Form\Action;

use Tk\CallbackCollection;
use Tk\Form\Field;
use Tk\Uri;

/**
 * @author Tropotek <http://www.tropotek.com/>
 */
abstract class ActionInterface extends Field\FieldInterface
{

    protected CallbackCollection $callbackList;

    protected ?Uri $redirect = null;


    public function __construct(string $name, $type = 'button', ?callable $callback = null)
    {
        $this->callbackList = CallbackCollection::create();
        parent::__construct($name);
        $this->appendCallback($callback);
    }

    /**
     * Execute this events callback methods/functions
     */
    public function execute(array $values = []): void
    {
        $this->getCallbackList()->execute($this->getForm(), $this);
        if ($this->getRedirect()) {
            Uri::create($this->getRedirect())->redirect();
        }
    }


    public function getCallbackList(): CallbackCollection
    {
        return $this->callbackList;
    }

    /**
     * Add a callback to the start of the event queue
     * function (\Tk\Form $form, \Tk\Form\Event\Iface $event) {}
     */
    public function prependCallback(callable $callback, int $priority = CallbackCollection::DEFAULT_PRIORITY): static
    {
        $this->getCallbackList()->prepend($callback, $priority);
        return $this;
    }

    /**
     * Add a callback to the end of the event queue
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

    public function getEventName(): string
    {
        return $this->makeInstanceKey($this->getName());
    }

    public function getValue(): string
    {
        return $this->getName();
    }

}