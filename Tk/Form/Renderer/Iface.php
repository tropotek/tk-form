<?php
namespace Tk\Form\Renderer;

use \Tk\Form;
use \Tk\Form\Field;
use Dom\Template;

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



}