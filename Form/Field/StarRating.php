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

    protected $min = -10;

    protected $max = 10;

    protected $step = 1;


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
        $this->addCssClass('StarRating');
        $this->setName($name);
        $this->setLabel(self::makeLabel($name));
        $this->setType($type);
        if (!$this->getType()) {
            $this->setType(new \Form\Type\Integer());
        }
    }

    /**
     * show
     */
    public function show()
    {
        parent::show();
        $template = $this->getTemplate();


        // Star Rating scripts
        $template->appendCssUrl(\Tk\Url::create('/vendor/kartik-v/bootstrap-star-rating/css/star-rating.min.css'));
        $template->appendJsUrl(\Tk\Url::create('/vendor/kartik-v/bootstrap-star-rating/js/star-rating.min.js'));
        $js = <<<JS
jQuery(function($) {
  $('.StarRating input, input.ratingValue').rating({'min': {$this->min}, 'max': {$this->max}, 'step': {$this->step}, 'size': 'xs', 'showClear': false, 'showCaption': false});
  $('.StarRating input').on('rating.change', function(event, value, caption) {
    var total = 0;
    var count = 0;
    $('.StarRating input').each(function (i) {
      count++;
      total += parseFloat($(this).val());
    });
    $(this).parents('fieldset.Rate').find('.totalValue').text(parseFloat(total/count).toFixed(2));
  });
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