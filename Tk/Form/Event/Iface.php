<?php
namespace Tk\Form\Event;

use Tk\Form\Exception;
use Tk\Form\Field;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
abstract class Iface extends Field\Iface
{
    /**
     * @var callable[]
     */
    protected $callbackList = array();

    /**
     * @var null|\Tk\Uri
     */
    protected $redirect = null;


    /**
     * __construct
     *
     * @param string $name
     * @param null|callable $callback
     * @param null|\Tk\Uri $redirect
     */
    public function __construct($name, $callback = null, $redirect = null)
    {
        parent::__construct($name);
        $this->prependCallback($callback);
        $this->setRedirect($redirect);
    }

    /**
     * Execute this events callback methods/functions
     */
    public function execute()
    {
        foreach ($this->callbackList as $i => $callback) {
            call_user_func_array($callback, array($this->getForm(), $this));
        }
        if ($this->getRedirect()) {
            \Tk\Uri::create($this->getRedirect())->redirect();
        }
    }


    /**
     * Add a callback to the start of the event queue
     * function (\Tk\Form $form, \Tk\Form\Event\Iface $event) {}
     *
     * @param callable $callback
     * @return $this
     */
    public function prependCallback($callback)
    {
        if (is_callable($callback))
            array_unshift($this->callbackList, $callback);
        return $this;
    }

    /**
     * Add a callback to the end of the event queue
     * function (\Tk\Form $form, \Tk\Form\Event\Iface $event) {}
     *
     * @param callable $callback
     * @return $this
     * @since 2.0.68
     */
    public function appendCallback($callback)
    {
        if (is_callable($callback))
            $this->callbackList[] = $callback;
        return $this;
    }

    /**
     * getEvent
     *
     * @return array
     */
    public function getCallbackList()
    {
        return $this->callbackList;
    }

    /**
     * @param array $list
     * @return $this
     */
    public function setCallbackList($list = array())
    {
        $this->callbackList = $list;
        return $this;
    }

    /**
     * @return null|\Tk\Uri
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @param null|\Tk\Uri $redirect
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
    public function isArrayField()
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
        if (is_callable($callback))
            $this->callbackList[] = $callback;
        return $this;
    }
}