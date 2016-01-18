<?php
namespace Tk\Form\Renderer;

use \Tk\Form;
use \Tk\Form\Field;

/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
abstract class Iface extends \Dom\Renderer\Renderer
{

    /**
     * @var Form
     */
    protected $form = null;


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
     * Render the form field values
     *
     * @param Form\Element $field
     * @return mixed
     */
    abstract protected function showField(Form\Element $field);



}