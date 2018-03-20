<?php
namespace Tk\Form\Renderer;

use \Tk\Form;
use \Tk\Form\Field;
use Dom\Template;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
abstract class Iface extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{

    /**
     * @var Form
     */
    protected $form = null;

    /**
     * @var null|\Tk\Event\Dispatcher
     */
    protected $dispatcher = null;


    /**
     * construct
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    /**
     * Get the form
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return null|\Tk\Event\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param null|\Tk\Event\Dispatcher $dispatcher
     */
    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }


}