<?php
namespace Tk\Form\Renderer;

use Tk\Form;

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
     * @var null|\Tk\EventDispatcher\EventDispatcher
     */
    protected $dispatcher = null;

    /**
     * @var null|\Tk\Form\Renderer\FieldGroup
     */
    protected $fieldGroupRenderer = null;

    /**
     * @var null|Layout
     */
    protected $layout = null;


    /**
     * construct
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
        $this->setLayout(new Layout());
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
     * @param Form $form
     * @return Iface
     */
    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return null|\Tk\EventDispatcher\EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param null|\Tk\EventDispatcher\EventDispatcher $dispatcher
     */
    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return null|FieldGroup
     */
    public function getFieldGroupRenderer()
    {
        return $this->fieldGroupRenderer;
    }

    /**
     * @param null|FieldGroup $fieldGroupRenderer
     * @return static
     */
    public function setFieldGroupRenderer($fieldGroupRenderer)
    {
        $this->fieldGroupRenderer = $fieldGroupRenderer;
        return $this;
    }

    /**
     * @return Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param null|Layout $layout
     * @return static
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }


}