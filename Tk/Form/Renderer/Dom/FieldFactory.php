<?php
namespace Tk\Form\Renderer\Dom;

use \Tk\Form\Type;

/**
 * Class FieldFactory
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class FieldFactory
{

    /**
     * Create A Field
     *
     * @param string $name
     * @param callable $callback
     * @return \Tk\Form\Field\Button
     */
    public static function createButton($name, $callback = null)
    {
        $field = new \Tk\Form\Field\Button($name, $callback);
        $field->setRenderer(new Field\Button($field));
        return $field;
    }

    /**
     * Create A Field
     *
     * @param string $name
     * @return \Tk\Form\Field\Checkbox
     */
    public static function createCheckbox($name)
    {
        $field = new \Tk\Form\Field\Checkbox($name, new Type\Boolean());
        $field->setRenderer(FieldGroup::create(new Field\Checkbox($field)));
        return $field;
    }

    /**
     * Create A Field
     *
     * @param string $name
     * @return \Tk\Form\Field\Checkbox
     */
    public static function createDate($name)
    {
        $field = new \Tk\Form\Field\Input($name, new Type\DateTime());
        $field->setRenderer(FieldGroup::create(new Field\Date($field)));
        return $field;
    }

    /**
     * Create A Field
     *
     * @param string $name
     * @return \Tk\Form\Field\File
     */
    public static function createFile($name)
    {
        $field = new \Tk\Form\Field\File($name);
        $field->setRenderer(FieldGroup::create(new Field\File($field)));
        return $field;
    }

    /**
     * Create A Field
     *
     * @param string $name
     * @return \Tk\Form\Field\Input
     */
    public static function createHidden($name)
    {
        $field = new \Tk\Form\Field\Input($name);
        $field->setRenderer(new Field\Hidden($field));
        return $field;
    }

    /**
     * Create A Field
     *
     * @param string $name
     * @param string|\Tk\Url $url
     * @param callable $callback
     * @return \Tk\Form\Field\Link
     */
    public static function createLink($name, $url = null, $callback = null)
    {
        $field = new \Tk\Form\Field\Link($name, $url, $callback);
        $field->setRenderer(new Field\Link($field));
        return $field;
    }

    /**
     * Create A Field
     *
     * @param string $name
     * @return \Tk\Form\Field\Input
     */
    public static function createPassword($name)
    {
        $field = new \Tk\Form\Field\Input($name);
        $field->setRenderer(FieldGroup::create(new Field\Password($field)));
        return $field;
    }

    /**
     * Create A Field
     *
     * @param string $name
     * @return \Tk\Form\Field\Input
     */
    public static function createHtml($name, $html)
    {
        $field = new \Tk\Form\Field\Input($name, new Type\Null());
        $field->setRenderer(FieldGroup::create(new Field\Html($field, $html)));
        return $field;
    }

    /**
     * Create A Field
     *
     * @param string $name
     * @param \Tk\Form\Field\Option\ArrayIterator $optionIterator
     * @return \Tk\Form\Field\Input
     */
    public static function createSelect($name, $optionIterator = null)
    {
        $field = new \Tk\Form\Field\SelectList($name, $optionIterator);
        $field->setRenderer(FieldGroup::create(new Field\Select($field)));
        return $field;
    }

    /**
     * Create A Field
     *
     * @param string $name
     * @return \Tk\Form\Field\Input
     */
    public static function createText($name)
    {
        $field = new \Tk\Form\Field\Input($name);
        $field->setRenderer(FieldGroup::create(new Field\Text($field)));
        return $field;
    }

    /**
     * Create A Field
     *
     * @param string $name
     * @return \Tk\Form\Field\Input
     */
    public static function createTextarea($name)
    {
        $field = new \Tk\Form\Field\Input($name);
        $field->setRenderer(FieldGroup::create(new Field\Textarea($field)));
        return $field;
    }

}