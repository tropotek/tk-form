/*
 * Plugin: gmapSelect
 * Version: 1.0
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 *
 * <code>
 *   $(document).ready(function() {
 *     // attach the plugin to an element
 *     $('#element').pluginName({'foo': 'bar'});
 *
 *     // call a public method
 *     $('#element').data('pluginName').foo_public_method();
 *
 *     // get the value of a property
 *     $('#element').data('pluginName').settings.foo;
 *
 *   });
 * </code>
 *
 *
 * NOTE: The elemeent is the canvas for the map not a form element...
 *
 */

(function($) {
  var gmapSelect = function(element, options) {
    // plugin vars
    var defaults = {
      lat: -37.797441,
      lng: 144.960773,
      zoom: 14,
      elementNames: ['Lat', 'Lng', 'Zoom'],
      icon: null,
      onSelect: function(lat, lng, zoom) {
        form.find('input[name="'+plugin.settings.name+plugin.settings.elementNames[0]+'"]').attr('value', lat);
        form.find('input[name="'+plugin.settings.name+plugin.settings.elementNames[1]+'"]').attr('value', lng);
        form.find('input[name="'+plugin.settings.name+plugin.settings.elementNames[2]+'"]').attr('value', zoom);
      },
      onChange: function(lat, lng, zoom) {
        form.find('input[name="'+plugin.settings.name+plugin.settings.elementNames[2]+'"]').attr('value', zoom);
      }
    };
    var plugin = this;
    plugin.settings = {};
    var map = null;
    var marker = null;
    var dragStartPosition = null;
    var form = null;

    // constructor method
    plugin.init = function() {
      plugin.settings = $.extend({}, defaults, $(element).data(), options);
      form = $(element).closest('form');

      // Init Map
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
      plugin.settings.map = map = new google.maps.Map(element, {
        zoom: plugin.settings.zoom,
        center: plugin.getLatLng(),
        streetViewControl: false,
        panControl: false,
        fullscreenControl: false,
        //disableDefaultUI: true,
        styles : myStyles,
        mapTypeId: google.maps.MapTypeId.ROADMAP  // ROADMAP, SATELLITE, TERRAIN, HYBRID
      });

      // check for tabs and add fix
      if ($(element).parents('.tab-pane').length) {
        $(element).parents('.tab-pane').each(function () {
          $('a[href=\'#'+this.id+'\']').on('mouseup', function(e) {
            plugin.redraw();
          });
        });
      } else {
        plugin.redraw();
      }

      plugin.settings.marker = marker = new google.maps.Marker({
        position: plugin.getLatLng(),
        title: '',
        map: map,
        animation: google.maps.Animation.DROP,
        draggable: true,
        icon: plugin.settings.icon
      });


      google.maps.event.addListener(marker, 'dragstart', function() {
        dragStartPosition = marker.getPosition();
      });

      google.maps.event.addListener(marker, 'dragend', function() {
        if (plugin.settings.onSelect.call(map, marker.getPosition().lat(), marker.getPosition().lng(), map.getZoom()) === false) marker.setPosition(dragStartPosition);
        if (plugin.settings.onChange.call(map, marker.getPosition().lat(), marker.getPosition().lng(), map.getZoom()) === false) marker.setPosition(dragStartPosition);
        plugin.setLatLng(marker.getPosition().lat(), marker.getPosition().lng());
      });
      google.maps.event.addListener(map, 'click', function(e) {
        if (plugin.settings.onSelect.call(map, e.latLng.lat(), e.latLng.lng(), map.getZoom()) === false) return false;
        if (plugin.settings.onChange.call(map, e.latLng.lat(), e.latLng.lng(), map.getZoom()) === false) return false;
        plugin.setLatLng(e.latLng.lat(), e.latLng.lng());
      });
      google.maps.event.addListener(map, 'zoom_changed', function() {
        if (plugin.settings.onChange.call(map, plugin.settings.lat, plugin.settings.lng, map.getZoom()) === false) return false;
        plugin.setZoom(map.getZoom());
      });

      $(element).show();
    };

    // public methods

    plugin.getLatLng = function() {
      return {lat: plugin.settings.lat, lng: plugin.settings.lng};
    };

    plugin.getZoom = function() {
      return plugin.settings.zoom;
    };

    plugin.setLatLng = function(lat, lng) {
      plugin.settings.lat = lat;
      plugin.settings.lng = lng;
      marker.setPosition(plugin.getLatLng());
      //plugin.redraw();
    };

    plugin.setZoom = function(zoom) {
      plugin.settings.zoom = zoom;
    };

    plugin.redraw = function() {
      setTimeout(function () {
        google.maps.event.trigger(map, 'resize');
        map.setCenter(plugin.getLatLng());
        map.setZoom(plugin.settings.zoom);
      }, 40);
    };

    plugin.getMap = function() {
      return map;
    };

    plugin.getMarker = function() {
      return marker;
    };


    // private methods
    //var foo_private_method = function() { };

    // call the "constructor" method
    plugin.init();
  };

  // add the plugin to the jQuery.fn object
  $.fn.gmapSelect = function(options) {
    return this.each(function() {
      if (undefined === $(this).data('gmapSelect')) {
        var plugin = new gmapSelect(this, options);
        $(this).data('gmapSelect', plugin);
      }
    });
  }

})(jQuery);