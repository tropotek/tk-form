<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 *  A allows you to select a lat/lng and zoom option
 *  At the moment this requires 3 existing elements (hidden or text fields to work)
 * EG:
 * <code>
 *
 * </code>
 *
 *
 * NOTE: Currently not tab enabled, so this field must be shown on page-load, not a hidden div.
 * @package Form\Field
 */
class GmapSelect extends Iface
{

    protected $hideFields = false;

    /**
     * __construct
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name, new \Form\Type\Gmap());
        $this->addStyle('width', '100%');
        $this->addStyle('height', '400px');
    }

    function getFieldName($fieldName = '')
    {
        if ($fieldName) {
            return $this->getName().ucfirst($fieldName);
        }
        return $this->getName();
    }

    function getFieldId($fieldName = '')
    {
        $str = 'fid';
        if ($this->getForm()) {
            $str = $this->getForm()->getId();
        }
        return $str . '_' . $this->getFieldName($fieldName);
    }

    function getMapId($fieldName = '')
    {
        return $this->getFieldName($fieldName).$this->getForm()->getInstanceId();
    }

    /**
     * @param bool $b
     * @return $this
     */
    function hideFields($b = true)
    {
        $this->hideFields = $b;
        return $this;
    }

    /**
     * TODO: Check GMap type and see why this is not
     * needed there, probably something to do with the
     * the date being an object and the maps being strings.... Check it out
     *
     *
     * @return bool
     */
    public function isMultiField()
    {
        return true;
    }

    /**
     * Render the widget.
     *
     */
    public function show()
    {
        $t = $this->getTemplate();


        if (!$this->enabled) {
            $t->setAttr('lat', 'disabled', 'disabled');
            $t->setAttr('lng', 'disabled', 'disabled');
            $t->setAttr('zoom', 'disabled', 'disabled');
        }
        if ($this->required && !$this->form->hasTabGroups()) {
            $t->setAttr('lat', 'required', 'required');
            $t->setAttr('lng', 'required', 'required');
            $t->setAttr('zoom', 'required', 'required');
        }
        if ($this->readonly) {
            $t->setAttr('lat', 'readonly', 'readonly');
            $t->setAttr('lng', 'readonly', 'readonly');
            $t->setAttr('zoom', 'readonly', 'readonly');
        }
        if (!$this->autocomplete) {
            $t->setAttr('lat', 'autocomplete', 'off');
            $t->setAttr('lng', 'autocomplete', 'off');
            $t->setAttr('zoom', 'autocomplete', 'off');
        }

        $t->setAttr('lat', 'name', $this->getFieldName('lat'));
        $t->setAttr('lng', 'name', $this->getFieldName('lng'));
        $t->setAttr('zoom', 'name', $this->getFieldName('zoom'));
        $t->setAttr('search', 'name', $this->getFieldName('search'));

        $t->setAttr('lat', 'id', $this->getFieldId('lat'));
        $t->setAttr('lng', 'id', $this->getFieldId('lng'));
        $t->setAttr('zoom', 'id', $this->getFieldId('zoom'));
        $t->setAttr('search', 'id', $this->getFieldId('search'));
        $t->setAttr('canvas', 'id', $this->getFieldId());

        $t->setAttr('lat', 'placeholder', 'Latitude');
        $t->setAttr('lng', 'placeholder', 'Longditude');
        $t->setAttr('zoom', 'placeholder', 'Zoom');

        if ($this->accessKey) {
            $t->setAttr('lat', 'accesskey', $this->accessKey);
        }
        if ($this->tabindex > 0) {
            $t->setAttr('lat', 'tabindex', $this->tabindex);
        }

        // Element Value
        $fieldValues = $this->getType()->getFieldValues();

        $n = $this->getFieldName().'Lat';
        if (isset($fieldValues[$n]) && !is_array($fieldValues[$n])) {
            $t->setAttr('lat', 'value', $fieldValues[$n]);
        }
        $n = $this->getFieldName().'Lng';
        if (isset($fieldValues[$n]) && !is_array($fieldValues[$n])) {
            $t->setAttr('lng', 'value', $fieldValues[$n]);
        }
        $n = $this->getFieldName().'Zoom';
        if (isset($fieldValues[$n]) && !is_array($fieldValues[$n])) {
            $t->setAttr('zoom', 'value', $fieldValues[$n]);
        }

        $style = '';
        if (isset($this->styleList['width'])) {
            $style .= 'width: ' . $this->styleList['width'] . '; ';
        }
        if (isset($this->styleList['height'])) {
            $style .= 'height: ' . $this->styleList['height'] . '; ';
        }
        if ($style) {
            $style = substr($style, 0 , -2);
            $t->setAttr('canvas', 'style', $style);
        }

        if ($this->hideFields) {
            $t->setAttr('lat', 'type', 'hidden');
            $t->setAttr('lng', 'type', 'hidden');
            $t->setAttr('zoom', 'type', 'hidden');
        } else {
            $t->setChoice('labels');
        }

    }

    /**
     * makeTemplate
     *
     * @return string
     */
    public function __makeTemplate()
    {
        $xmlStr = <<<XML
<?xml version="1.0"?>
<div class="tk-GmapSelect">
<script src="//maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false"></script>
<style>
.GmapSelect .tk-gmap-canvas img { max-width: none !important; }
.GmapSelect .tk-gmap-canvas {
  line-height: 1;margin: 10px 0px;padding: 0;
  border: 1px solid #CCCCCC;
  border-radius: 3px;
  box-shadow: 4px 4px 3px 0px rgba(20, 20, 20, 0.075);
}
</style>
<script>//<![CDATA[
function TkGoogleMap() {
    var _self = this;
    var _cfg = {
        eLat   : null,
        eLng   : null,
        eZoom  : null,
        canvas : null,

        geo    : null,
        map    : null,
        marker : null
    };

    this.init = function(opts) {
        _cfg = $.extend(_cfg, opts);

        google.maps.visualRefresh = true;
        var myStyles =[
            {
                featureType: "poi",
                elementType: "labels",
                stylers: [
                      { visibility: "off" }
                ]
            }
        ];
        _cfg.map = new google.maps.Map(_cfg.canvas, {
            zoom: _self.getZoom(),
            center: _self.getLatLng(),
            streetViewControl: false,
            panControl: false,
            disableDefaultUI: false,
            styles : myStyles,
            mapTypeId: google.maps.MapTypeId.ROADMAP  // ROADMAP, SATELLITE, TERRAIN, HYBRID
        });

        _cfg.marker = new google.maps.Marker({
            position: _self.getLatLng(),
            title: '',
            map: _cfg.map,
            draggable: true
        });

        // Add dragging event listeners.
        google.maps.event.addListener(_cfg.marker, 'drag', function() {
            _self.updateFields(_cfg.marker.getPosition(), _cfg.map.getZoom());
        });
        google.maps.event.addListener(_cfg.marker, 'dragend', function() {
            _self.updateFields(_cfg.marker.getPosition(), _cfg.map.getZoom());
        });
        google.maps.event.addListener(_cfg.map, 'click', function(e) {
          _self.updateFields(e.latLng, _cfg.map.getZoom());
          _cfg.marker.setPosition(e.latLng);
        });
        google.maps.event.addListener(_cfg.map, 'zoom_changed', function() {
          _self.updateFields(null, _cfg.map.getZoom());
        });

        // field functions
        $('.isMap').mouseup(function (e) {
            setTimeout(function () {
                google.maps.event.trigger(_cfg.map, 'resize');
                _cfg.map.setCenter(_self.getLatLng());
            }, 40);
        });

        $('.tk-form .GmapSelect #fid-GmapSearch').keypress(function (e) {
            var keyCode = (e.keyCode ? e.keyCode : e.which);
            if (keyCode == 13) {
                _self.selectAddress($(this).val());
                e.preventDefault();
                return false;
            }
        });

    }

    this.getMap = function() {
        return _cfg.map;
    }

    this.getZoom = function() {
        var eZoom = _cfg.eZoom;
        if (eZoom != null && eZoom.value != '') {
            if (parseInt(eZoom.value) > 16) return 16;
            if (parseInt(eZoom.value) < 1)  return 1;
            return parseInt(eZoom.value);
        }
        return 12;
    }

    this.getLatLng = function() {
        var eLat = _cfg.eLat;
        var eLng = _cfg.eLng;
        if (eLat && eLat.value && eLng && eLng.value ) {
            return new google.maps.LatLng(eLat.value, eLng.value);
        }
        return new google.maps.LatLng(-37.819, 144.951);
    }

    this.updateMap = function(latlng) {
        var zoom = arguments[1] ? arguments[1] : null;
        _cfg.map.setCenter(latlng);
        if (zoom)
            _cfg.map.setZoom(zoom);
    }

    this.updateFields = function(latlng, zoom) {
        if (latlng && _cfg.eLat && !_cfg.eLat.disabled)  _cfg.eLat.value = latlng.lat();
        if (latlng && _cfg.eLng && !_cfg.eLng.disabled)  _cfg.eLng.value = latlng.lng();
        if (zoom && _cfg.eZoom && !_cfg.eZoom.disabled) _cfg.eZoom.value = zoom;
    }

    this.selectAddress = function(address) {
        var zoom = arguments[1] ? arguments[1] : 8;
        if (!_cfg.geo)
            _cfg.geo = new google.maps.Geocoder();

        _cfg.geo.geocode( {'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                _self.updateFields(results[0].geometry.location, zoom);
                _self.updateMap(results[0].geometry.location);
            } else {
                //_self.error('Geocode was not successful for the following reason: ' + status);
                //alert('Unable locate company. Please check the address and/or locate the company manually.');
                _self.updateFields(new google.maps.LatLng(-37.77071473849609, 144.898681640625), zoom);
                _self.updateMap(new google.maps.LatLng(-37.77071473849609, 144.898681640625));
            }
            if (zoom)
                _cfg.map.setZoom(zoom);
        });
    }

    this.error = function(msg) {
        if (console.log) {
            console.log(msg);
        }
    }

};


// Initalise `tk-GmapSelect` Fields
jQuery(function($) {
    $('.GmapSelect .tk-GmapSelect').each(function (i, ebox) {
        ebox.tkGoogleMap = new TkGoogleMap();
        ebox.tkGoogleMap.init({
            eLat   : $(ebox).find('.tk-gmap-lat').get(0),
            eLng   : $(ebox).find('.tk-gmap-lng').get(0),
            eZoom  : $(ebox).find('.tk-gmap-zoom').get(0),
            canvas : $(ebox).find('.tk-gmap-canvas').get(0)
        });
        $(ebox).find('.tk-gmap-searchBtn').click( function() { ebox.tkGoogleMap.selectAddress($(ebox).find('.tk-gmap-address').val()); } );

    });
});
//]]>
</script>
  <div class="tk-gmap-searchBox">
    <input type="text" var="search" class="tk-gmap-address" style="width: 420px;" title="Search for a location" placeholder="Find A Location" />
    <button type="button" class="tk-gmap-searchBtn noblock"><span>Locate</span></button>
  </div>
  <div class="tk-gmap-canvas" var="canvas"></div>
  <div class="tk-gmap-fieldGroup">
    <span choice="label">Lat:</span> <input type="text" var="lat" class="tk-gmap-lat" style="width: 120px;" />
    <span choice="label">Long:</span> <input type="text" var="lng" class="tk-gmap-lng" style="width: 120px;" />
    <span choice="label">Zoom:</span> <input type="text" var="zoom" class="tk-gmap-zoom" style="width: 50px;" />
  </div>
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }


}