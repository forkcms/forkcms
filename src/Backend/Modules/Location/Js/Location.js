/**
 * Interaction for the location module
 */
jsBackend.location =
{
    // base values
    bounds: null, center: null, centerLat: null, centerLng: null, height: null,
    map: null, mapId: null, panorama: null, showDirections: false, showLink: false, showOverview: true,
    type: null, style: null, width: null, zoomLevel: null,

    init: function()
    {
        // only show a map when there are options and markers given
        if($('#map').length > 0 && typeof markers != 'undefined' && typeof mapOptions != 'undefined')
        {
            jsBackend.location.showMap();

            // add listeners for the zoom level and terrain
            google.maps.event.addListener(jsBackend.location.map, 'maptypeid_changed', jsBackend.location.setDropdownTerrain);
            google.maps.event.addListener(jsBackend.location.map, 'zoom_changed', jsBackend.location.setDropdownZoom);

            // if the zoom level or map type changes in the dropdown, the map needs to change
            $('#zoomLevel').bind('change', function() { jsBackend.location.setMapZoom($('#zoomLevel').val()); });
            $('#mapType').bind('change', jsBackend.location.setMapTerrain);
            $('#mapStyle').bind('change', jsBackend.location.setMapStyle);

            // the panning save option
            $('#saveLiveData').bind('click', function(e)
            {
                e.preventDefault();

                // save the live map data
                jsBackend.location.getMapData();
                jsBackend.location.saveLiveData();
            });
        }
    },

    /**
     * Add marker to the map
     *
     * @param object map
     * @param object bounds
     * @param object object
     */
    addMarker: function(map, bounds, object)
    {
        position = new google.maps.LatLng(object.lat, object.lng);
        bounds.extend(position);

        // add marker
        var marker = new google.maps.Marker(
        {
            position: position,
            map: map,
            title: object.title
        });

        if(typeof object.dragable != 'undefined' && object.dragable)
        {
            marker.setDraggable(true);

            // add event listener
            google.maps.event.addListener(marker, 'dragend', function()
            {
                jsBackend.location.updateMarker(marker);
            });
        }

        // add click event on marker
        google.maps.event.addListener(marker, 'click', function()
        {
            // create infowindow
            new google.maps.InfoWindow(
            {
                content: '<h1>'+ object.title +'</h1>' + object.text
            }).open(map, marker);
        });
    },

    /**
     * Get map data
     */
    getMapData: function()
    {
        // get the live data
        jsBackend.location.zoomLevel = jsBackend.location.map.getZoom();
        jsBackend.location.type = jsBackend.location.map.getMapTypeId();
        jsBackend.location.style = jsBackend.location.getMapStyle();
        jsBackend.location.center = jsBackend.location.map.getCenter();
        jsBackend.location.centerLat = jsBackend.location.center.lat();
        jsBackend.location.centerLng = jsBackend.location.center.lng();

        // get the form data
        jsBackend.location.mapId = parseInt($('#mapId').val());
        jsBackend.location.height = parseInt($('#height').val());
        jsBackend.location.width = parseInt($('#width').val());

        jsBackend.location.showDirections = ($('#directions').attr('checked') == 'checked');
        jsBackend.location.showLink = ($('#fullUrl').attr('checked') == 'checked');
        jsBackend.location.showOverview = ($('#markerOverview').attr('checked') == 'checked');
    },

    /**
     * Panorama visibility changed
     */
    panoramaVisibilityChanged: function(e)
    {
        // panorama is now invisible
        if(!jsBackend.location.panorama.getVisible())
        {
            // select default map type
            $('#mapType option:first-child').attr("selected", "selected");

            // set map terrain
            jsBackend.location.setMapTerrain();
        }
    },

    /**
     * Refresh page refresh the page and display a certain message
     *
     * @param string message
     */
    /**
     * Get map style
     */
    getMapStyle: function()
    {
        return $('#mapStyle').find('option:selected').val();
    },

    // this will refresh the page and display a certain message
    refreshPage: function(message)
    {
        var currLocation = window.location;
        var reloadLocation = (currLocation.search.indexOf('?') >= 0) ? '&' : '?';
        reloadLocation = currLocation + reloadLocation + 'report=' + message;

        // cleanly redirect so we can display a message
        window.location = reloadLocation;
    },

    /**
     * Save live data will save the setting in database
     */
    saveLiveData: function()
    {
        $.ajax(
        {
            data:
            {
                fork: { module: 'Location', action: 'SaveLiveLocation' },
                zoom: jsBackend.location.zoomLevel,
                type: jsBackend.location.type,
                style: jsBackend.location.style,
                centerLat: jsBackend.location.centerLat,
                centerLng: jsBackend.location.centerLng,
                height: jsBackend.location.height,
                width: jsBackend.location.width,
                id: jsBackend.location.mapId,
                link: jsBackend.location.showLink,
                directions: jsBackend.location.showDirections,
                showOverview: jsBackend.location.showOverview
            },
            success: function(json, textStatus)
            {
                // reload the page on success
                if(json.code == 200)
                {
                    // no redirect given, refresh the page
                    if(typeof $('input#redirect').val() == 'undefined')
                    {
                        jsBackend.location.refreshPage('map-saved');
                    }

                    $('input#redirect').val('edit');
                    $('form#edit').submit();
                }
            }
        });
    },

    /**
     * Set dropdown terrain will set the terrain type of the map to the dropdown
     */
    setDropdownTerrain: function()
    {
        jsBackend.location.getMapData();
        $('#mapType').val(jsBackend.location.type.toUpperCase());
    },

    /**
     * Set dropdown zoom will set the zoom level of the map to the dropdown
     */
    setDropdownZoom: function()
    {
        jsBackend.location.getMapData();
        $('#zoomLevel').val(jsBackend.location.zoomLevel);
    },

    // this will set the theme style of the map to the dropdown
    setMapStyle: function()
    {
        jsBackend.location.style = $('#mapStyle').val();
        jsBackend.location.map.setOptions({'styles': MAPS_CONFIG[jsBackend.location.style]});
    },

  /**
   * Set map terrain will set the terrain type of the map to the dropdown
   */
    setMapTerrain: function()
    {
        // init previous type
        var previousType = jsBackend.location.type.toLowerCase();

        // redefine type
        jsBackend.location.type = $('#mapType').val().toLowerCase();

        // do something when we have street view
        if(previousType == 'street_view' || jsBackend.location.type == 'street_view')
        {
            // init showPanorama if not yet initialised
            if(jsBackend.location.panorama == null)
            {
                // init panorama street view
                jsBackend.location.showPanorama();
            }

            // toggle visiblity
            jsBackend.location.panorama.setVisible((jsBackend.location.type == 'street_view'));
        }

        // set map type
        jsBackend.location.map.setMapTypeId(jsBackend.location.type);
    },

    /**
     * Set map zoom will set the zoom level of the map to the dropdown
     *
     * @param int zoomlevel
     */
    setMapZoom: function(zoomlevel)
    {
        jsBackend.location.zoomLevel = zoomlevel;

        // set zoom automatically, defined by points (if allowed)
        if(zoomlevel == 'auto') jsBackend.location.map.fitBounds(jsBackend.location.bounds);
        else jsBackend.location.map.setZoom(parseInt(zoomlevel));
    },

    /**
     * Show map
     */
    showMap: function()
    {
        // create boundaries
        jsBackend.location.bounds = new google.maps.LatLngBounds();

        // define type if not already set
        if(jsBackend.location.type == null) jsBackend.location.type = mapOptions.type;

        // define options
        var options =
        {
            center: new google.maps.LatLng(mapOptions.center.lat, mapOptions.center.lng),
            mapTypeId: eval('google.maps.MapTypeId.' + jsBackend.location.type),
      styles: MAPS_CONFIG[mapOptions.style]
        };

        // create map
        jsBackend.location.map = new google.maps.Map(document.getElementById('map'), options);

        // we want street view
        if(jsBackend.location.type == 'STREET_VIEW')
        {
            jsBackend.location.showPanorama();
            jsBackend.location.panorama.setVisible(true);
        }

        // loop the markers
        for(var i in markers)
        {
            jsBackend.location.addMarker(
                jsBackend.location.map, jsBackend.location.bounds, markers[i]
            );
        }

        jsBackend.location.setMapZoom(mapOptions.zoom);
    },

    /**
     * Show panorama - adds panorama to the map
     */
    showPanorama: function()
    {
        // get street view data from map
        jsBackend.location.panorama = jsBackend.location.map.getStreetView();

        // define position
        jsBackend.location.panorama.setPosition(new google.maps.LatLng(mapOptions.center.lat, mapOptions.center.lng));

        // define heading (horizontal °) and pitch (vertical °)
        jsBackend.location.panorama.setPov({
            heading: 200,
            pitch: 8,
            zoom: 1
        });

        // bind event listeners (possible functions: pano_changed, position_changed, pov_changed, links_changed, visible_changed)
        google.maps.event.addListener(jsBackend.location.panorama, 'visible_changed', jsBackend.location.panoramaVisibilityChanged);
    },

    /**
     * Update marker will re-set the position of a marker
     *
     * @param object marker
     */
    updateMarker: function(marker)
    {
        jsBackend.location.getMapData();

        var lat = marker.getPosition().lat();
        var lng = marker.getPosition().lng();

        $.ajax(
        {
            data:
            {
                fork: { module: 'Location', action: 'UpdateMarker' },
                id: jsBackend.location.mapId,
                lat: lat,
                lng: lng
            },
            success: function(json, textStatus)
            {
                // reload the page on success
                if(json.code == 200) jsBackend.location.saveLiveData();
            }
        });
    }
};

$(jsBackend.location.init);
