<?php
namespace Tk\Form\Event;

use Tk\Form\Exception;
use Tk\Form\Field;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
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
     * @param callable $callback
     * @param \Tk\Uri $redirect
     * @throws Exception
     */
    public function __construct($name, $callback = null, $redirect = null)
    {
        parent::__construct($name);
        $this->addCallback($callback);
        $this->setRedirect($redirect);
    }

    /**
     * Execute this events callback methods/functions
     * @throws \Exception
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
     *
     * @param callable $callback
     * @return $this
     * @throws Exception
     */
    public function prependCallback($callback)
    {
        if (!$callback) return $this;
        $this->validateCallback($callback);
        array_unshift($this->callbackList,  $callback);
        return $this;
    }

    /**
     * Add a callback to the end of the event queue
     *
     * @param callable $callback
     * @return $this
     * @throws Exception
     */
    public function addCallback($callback)
    {
        if (!$callback) return $this;
        $this->validateCallback($callback);
        $this->callbackList[] = $callback;
        return $this;
    }

    /**
     * Validate a callback parameter
     *
     * @param $callback
     * @throws Exception
     */
    protected function validateCallback($callback)
    {
        if (!is_callable($callback)) {
            if (is_array($callback) && !empty($callback[1])) {
                $class = get_class($callback[0]);
                $method = $callback[1];
                throw new Exception('Form event callback ' . $class.'::'.$method.'() cannot be found');
            } else if (is_string($callback)) {
                throw new Exception('Form event callback ' . $callback . ' cannot be found');
            }
            throw new Exception('Only callable values can be events. Check the method or function exists.');
        }
    }

    /**
     * getEvent
     *
     * @return callable[]\array
     */
    public function getCallbackList()
    {
        return $this->callbackList;
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
    
}