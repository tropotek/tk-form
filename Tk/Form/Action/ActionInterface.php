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


    public function __construct(string $name, ?callable $callback = null, ?Uri $redirect = null)
    {
        $this->callbackList = CallbackCollection::create();
        parent::__construct($name);
        $this->prependCallback($callback);
        $this->setRedirect($redirect);
    }

    /**
     * Execute this events callback methods/functions
     */
    public function execute()
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
     *
     * @param callable $callback
     * @param int $priority [optional]
     * @return $this
     */
    public function prependCallback(?callable $callback, $priority=Callback::DEFAULT_PRIORITY)
    {
        $this->getCallbackList()->prepend($callback, $priority);
        return $this;
    }

    /**
     * Add a callback to the end of the event queue
     * function (\Tk\Form $form, \Tk\Form\Event\Iface $event) {}
     *
     * @param callable $callback
     * @param int $priority [optional]
     * @return $this
     * @since 2.0.68
     */
    public function appendCallback(?callable $callback, $priority=Callback::DEFAULT_PRIORITY)
    {
        $this->getCallbackList()->append($callback, $priority);
        return $this;
    }

    /**
     * @return null|Uri
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @param null|Uri $redirect
     * @return $this
     */
    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
        return $this;
    }

    /**
     * 'fid-'.$form->getId().{$this->name}
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->makeInstanceKey($this->getName());
    }

    /**
     * Get the field value(s).
     *
     * @return string|array
     */
    public function getValue()
    {
        return $this->getName();
    }

    /**
     * isRequired
     *
     * @return boolean
     */
    public function isRequired()
    {
        return false;
    }

    /**
     * Does this fields data come as an array.
     * If the name ends in [] then it will be flagged as an arrayField.
     *
     * EG: name=`name[]`
     *
     * @return boolean
     */
    public function isMultiple()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getFieldset()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getTabGroup()
    {
        return '';
    }


    /**
     * @param $callback
     * @return $this
     * @deprecated use prependCallback
     * @remove 2.4.0
     */
    public function addCallback($callback)
    {
        if (!$callback) return $this;
        $this->appendCallback($callback);
        return $this;
    }
}