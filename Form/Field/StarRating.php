<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 *  A form text field object
 *
 * @note HTML5 element
 * @package Form\Field
 */
class StarRating extends Iface
{

    protected $min = 0;

    protected $max = 5;

    protected $step = 1;

    protected $showClear = false;

    protected $showCaption = true;

    protected $starCaptions = null;




    /**
     * __construct
     *
     * @param string $name
     * @param int $min
     * @param int $max
     * @param int $step
     * @param \Form\Type\Iface $type
     */
    public function __construct($name, $min = 0, $max = 5, $step = 1, $type = null)
    {
        $this->min = (int)$min;
        $this->max = (int)$max;
        $this->step = (int)$step;
        $this->setName($name);
        $this->setLabel(self::makeLabel($name));
        $this->setType($type);
        if (!$this->getType()) {
            $this->setType(new \Form\Type\Integer());
        }
    }

    /**
     *
     * @param $array
     * @return $this
     */
    public function setStarCaptions($array)
    {
        if (is_array($array)) {
            $this->starCaptions = $array;
        }
        return $this;
    }



    /**
     * show
     */
    public function show()
    {
        parent::show();
        $template = $this->getTemplate();

        $starCaptions = '';
        if (is_array($this->starCaptions) && count($this->starCaptions)) {
            $starCaptions = ', starCaptions: ' . json_encode($this->starCaptions);
        }

        // Star Rating scripts
        $template->appendCssUrl(\Tk\Url::create('/vendor/kartik-v/bootstrap-star-rating/css/star-rating.min.css'));
        $template->appendJsUrl(\Tk\Url::create('/vendor/kartik-v/bootstrap-star-rating/js/star-rating.min.js'));
        $js = <<<JS
jQuery(function($) {
  $('.StarRating input').rating({'min': {$this->min}, 'max': {$this->max}, 'step': {$this->step}, 'size': 'xs', 'showClear': false, 'showCaption': true $starCaptions });

});
JS;
        $template->appendJs($js);

//        $template->setAttr('element', 'type', 'range');
//        $template->setAttr('element', 'min', $this->min);
//        $template->setAttr('element', 'max', $this->max);
//        $template->setAttr('element', 'step', $this->step);

    }

    /**
     * makeTemplate
     *
     * @return string
     */
//    public function __makeTemplate()
//    {
//        $xmlStr = <<<XML
//< ?xml version="1.0"? >
//<input type="text" var="element" />
//XML;
//        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
//        return $template;
//    }
}