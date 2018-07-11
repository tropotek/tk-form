<?php
namespace Tk\Form\Field;


/**
 *
 * @note include the jquery.gmapSelect.js javascript to add a clickable map.
 *
 * Javascript example.
 * <code>
 *
 * // PHP:
 *   // include the google maps lib and jquery.gmapSelect.js plugin first
 *   // Async loading if not using jquery
 *   //  $template->appendJsUrl(\Tk\Uri::create('https://maps.googleapis.com/maps/api/js?key='.$gKey.'&libraries=places&callback=initAutocomplete'),
 *   //    array('async' => 'async', 'defer' => 'defer'));
 *   $template->appendJsUrl(\Tk\Uri::create('https://maps.googleapis.com/maps/api/js?key='.$gKey.'&libraries=places'));
 *   $template->appendJsUrl(\Tk\Uri::create('/src/App/Form/Field/jquery.gmapSelect.js'));
 *
 * // Javascript:
 * $('.tk-gmap-select .latlng').hide();    // Hide text fields
 * var gmapSelect = $('.tk-gmap-canvas').gmapSelect({
 *   onSelect: function(lat, lng, zoom) {
 *     $('#latFieldId').val(lat);
 *     $('#lngFieldId').val(lng);
 *     $('#zoomFieldId').val(zoom);
 *   }
 * });
 * </code>
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class GmapSelect extends \Tk\Form\Field\Iface
{

    /**
     * Assumes the field value resides within an array
     * EG:
     *   array(
     *    'fieldName1' => 'value1',
     *    'fieldName2' => 'value2',
     *    'fieldName3[]' => array('value3.1', 'value3.2', 'value3.3', 'value3.4'),  // same as below
     *    'fieldName3' => array('value3.1', 'value3.2', 'value3.3', 'value3.4')     // same
     * );
     *
     * This objects load() method is called by the form's execute() method
     *
     * @param array|\ArrayObject $values
     * @return $this
     */
    public function load($values)
    {
        // When the value does not exist it is ignored (may not be the desired result for unselected checkbox or empty select box)
        $vals = array();
        if (isset($values[$this->getName().'Lat'])) {
            $vals[$this->getName().'Lat'] =  (float)$values[$this->getName().'Lat'];
        }
        if (isset($values[$this->getName().'Lng'])) {
            $vals[$this->getName().'Lng'] =  (float)$values[$this->getName().'Lng'];
        }
        if (isset($values[$this->getName().'Zoom'])) {
            $z =(int)$values[$this->getName().'Zoom'];
            if ($z <= 0) {
                $z = 14;
            }
            $vals[$this->getName().'Zoom'] =  $z;
        }
        if (!count($vals)) $vals = null;

        $this->setValue($vals);
        return $this;
    }

    /**
     * Get the field value(s).
     *
     * @return string|array
     */
    public function getValue()
    {
        return parent::getValue();
    }

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     * @throws \Tk\Db\Exception
     */
    public function show()
    {
        $template = $this->getTemplate();
        \Bs\Ui\Js::includeGoogleMaps($template, array('libraries' => 'places'));
        $template->appendJsUrl(\Tk\Uri::create('/vendor/ttek/tk-form/js/jquery.gmapSelect.js'));

        $css = <<<CSS
.tk-gmap-select .tk-gmap-canvas {
  width: 100%;
  height: 500px;
}
CSS;
        $template->appendCss($css);

        $template->setAttr('lat', 'name', $this->getName().'Lat');
        $template->setAttr('lng', 'name', $this->getName().'Lng');
        $template->setAttr('zoom', 'name', $this->getName().'Zoom');
        
        $values = $this->getValue();
        $template->setAttr('lat', 'value', $values[$this->getName().'Lat']);
        $template->setAttr('lng', 'value', $values[$this->getName().'Lng']);
        $template->setAttr('zoom', 'value', $values[$this->getName().'Zoom']);
        
        $template->setAttr('lat', 'id', $this->getId().'_lat');
        $template->setAttr('lng', 'id', $this->getId().'_lng');
        $template->setAttr('zoom', 'id', $this->getId().'_zoom');

        $this->decorateElement($template);
        return $template;
    }

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="tk-gmap-select" var="mapSelect">
  <p class="latlng">
    <label>Lat: </label> <input type="text" name="Lat" class="form-control" var="lat" />
    <label>Lng: </label> <input type="text" name="Lng" class="form-control" var="lng" />
    <input type="hidden" name="Zoom" var="zoom" />
  </p>
  <div class="tk-gmap-canvas" var="canvas" style="display: none;"></div>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }
    
    
    
}