/*
 * Plugin: Example
 * Version: 1.0
 * Date: 11/05/17
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 * @source http://stefangabos.ro/jquery/jquery-plugin-boilerplate-revisited/
 */

/**
 * Use this scrip to manage the company address fields in a form
 *
 * Requires:
 * <code>
 *   $template->appendJsUrl(\Tk\Uri::create('https://maps.googleapis.com/maps/api/js?key='.$gKey.'&libraries=places'));
 *   $template->appendJsUrl(\Tk\Uri::create('/src/App/Form/Field/jquery.gmapSelect.js'));
 *   $template->appendJsUrl(\Tk\Uri::create('/src/App/Controller/Company/jquery.tkAddress.js'));
 * </code>
 *
 * <code>
 *   $(document).ready(function() {
 *     // attach the plugin to an element
 *     $('#element').tkAddress({'foo': 'bar'});
 *
 *     // call a public method
 *     $('#element').data('tkAddress').foo_public_method();
 *
 *     // get the value of a property
 *     $('#element').data('tkAddress').settings.foo;
 *
 *   });
 * </code>
 *
 * @TODO:
 * @TODO:
 * @TODO: We need to refactor this when used with the gmap.select.js
 * @TODO: Currently this is not in a very modular state but it works, (see the institution edit page)
 * @TODO:
 * @TODO:
 *
 */
(function ($) {
  /**
   *
   * @param element Form
   * @param options Object
   */
  var tkAddress = function (element, options) {
    // plugin vars
    var defaults = {
      gmapSelector: '.tk-gmap-select',
      markerIcon: null,
      fieldNames: {
        address: 'address',
        street: 'street',
        city: 'city',
        state: 'state',
        postcode: 'postcode',
        country: 'country',
        lat: 'mapLat',
        lng: 'mapLng',
        zoom: 'mapZoom'
      },
      lang: {
        updateAddress: 'Do you wish to update the address fields to this new location?'
      },

      onFoo: function () {
      }
    };
    var $element = $(element);
    var plugin = this;
    plugin.settings = {};
    var fields = {};
    var autocomplete;
    var form = null;

    // constructor method
    plugin.init = function () {
      plugin.settings = $.extend({}, defaults, $(element).data(), options);
      form = $(element).closest('form');


      // Get all field elements
      for (var fieldName in plugin.settings.fieldNames) {
        var name = plugin.settings.fieldNames[fieldName];
        fields[fieldName] = form.find('[name='+name+']');
        if (!fields[fieldName].length) {
          console.log('The address auto-complete functions will not work unless all fields are available.');
        }
      }

      if ($element.attr('data-marker-icon') !== undefined && plugin.settings.markerIcon === null) {
        plugin.settings.markerIcon = $element.attr('data-marker-icon');
      }

      // Not the best solution but this stops the form from submitting when enter is hit on this field
      fields.address.on('focus', geolocate).on('keydown', function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (13 === code) return false;
      });

      // Setup the autocomplete `address` field
      autocomplete = new google.maps.places.Autocomplete(
        (fields.address.get(0)),   /** @type {!HTMLInputElement} */
        {types: ['geocode']}
      );

      // When the user selects an address from the dropdown, populate the address fields in the form.
      autocomplete.addListener('place_changed', function () {
        var place = autocomplete.getPlace();
        updateAddressFields(place);
        if (place.geometry) {
          fields.lat.val(place.geometry.location.lat());
          fields.lng.val(place.geometry.location.lng());
          gmapSelect.data('gmapSelect').setLatLng(place.geometry.location.lat(), place.geometry.location.lng());
          gmapSelect.data('gmapSelect').redraw();
        }
      });


      // ---------------------------------------
      // Setup gmap select fields javascript
      // ---------------------------------------
      form.find(plugin.settings.gmapSelector + ' .latlng').hide();    // Hide map input fields
      // TODO: Removed this as I am not sure is is still required????
      // $.each(fields, function (i, el){
      //   if (el.attr('name') === plugin.settings.fieldNames['address'] ||
      //     el.attr('name') === plugin.settings.fieldNames['lat'] ||
      //     el.attr('name') === plugin.settings.fieldNames['lng'] ||
      //     el.attr('name') === plugin.settings.fieldNames['zoom']
      //   ) return;
      //   //el.closest('.form-row').hide();
      // });

      var gmapSelect = form.find(plugin.settings.gmapSelector + ' .tk-gmap-canvas').gmapSelect({
        lat: parseFloat(fields.lat.val()),
        lng: parseFloat(fields.lng.val()),
        zoom: parseFloat(fields.zoom.val()),
        icon: plugin.settings.markerIcon,
        onChange: function (lat, lng, zoom) {
          fields.zoom.val(zoom);
        },
        onSelect: function(lat, lng, zoom) {
          if (fields['address'].val() === '' || confirm(plugin.settings.lang.updateAddress)) {
            fields.lat.val(lat);
            fields.lng.val(lng);
            fields.zoom.val(zoom);

            var geo = new google.maps.Geocoder();
            geo.geocode({'location': {lat: lat, lng: lng}}, function(results, status) {
              if (status === 'OK' && results[0]) updateAddressFields(results[0]);
            });
          } else {
            return false;
          }
        }
      });

    };  // END init()



    // update the address fields with the place data
    function updateAddressFields(place) {
      for (var field in fields) {
        if (field === 'zoom' || field === 'lat' || field === 'lng') continue;
        //if (field === 'zoom') continue;
        fields[field].val('');
        fields[field].removeAttr('disabled');
      }
      // Get each component of the address from the place details
      // and fill the corresponding field on the form.
      if (place.address_components) {
        var num = '';
        fields.address.val(place.formatted_address);
        for (var i = 0; i < place.address_components.length; i++) {
          var addressType = place.address_components[i].types[0];
          var value = place.address_components[i]['long_name'];

          if (addressType === 'street_number') {  // Street number
            num = value;
          } else if (addressType === 'route') {    // Street name
            fields.street.val(num + ' ' + value);
          } else if (addressType === 'locality') {   // City
            fields.city.val(value);
          } else if (addressType === 'administrative_area_level_1') {   // state
            fields.state.val(value);
          } else if (addressType === 'country') {   // country
            fields.country.val(value);
          } else if (addressType === 'postal_code') {   // postcode
            fields.postcode.val(value);
          }
        }
      } else if (place.name) {
        fields.address.val(place.name);
      }
      // if (place.geometry) {
      //   fields.lat.val(place.geometry.location.lat());
      //   fields.lng.val(place.geometry.location.lng());
      // }
    }



    // Bias the autocomplete object to the user's geographical location,
    // as supplied by the browser's 'navigator.geolocation' object.
    function geolocate() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
          var geolocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
          var circle = new google.maps.Circle({
            center: geolocation,
            //radius: position.coords.accuracy
            radius: 2000
          });
          autocomplete.setBounds(circle.getBounds());
        });
      }
    }


    // private methods
    //var foo_private_method = function() { };

    // public methods
    //plugin.foo_public_method = function() { };

    // call the "constructor" method
    plugin.init();
  };

  // add the plugin to the jQuery.fn object
  $.fn.tkAddress = function (options) {
    return this.each(function () {
      if (undefined === $(this).data('tkAddress')) {
        var plugin = new tkAddress(this, options);
        $(this).data('tkAddress', plugin);
      }
    });
  }

})(jQuery);
