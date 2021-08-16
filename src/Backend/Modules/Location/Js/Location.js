import { Stylers } from './Components/Stylers'

export class Location {
  constructor () {
    // base values
    this.bounds = null
    this.center = null
    this.centerLat = null
    this.centerLng = null
    this.height = null
    this.map = null
    this.mapId = null
    this.panorama = null
    this.showDirections = false
    this.showLink = false
    this.showOverview = true
    this.type = null
    this.style = null
    this.width = null
    this.zoomLevel = null

    $('[data-role=toggle-settings]').on('change', () => {
      $('#settings').hide()
      if ($('[data-role=toggle-settings]').is(':checked')) {
        $('#settings').show()
      }
    }).change()

    // only show a map when there are options and markers given
    if ($('#map').length > 0 && typeof markers !== 'undefined' && typeof mapOptions !== 'undefined') {
      this.showMap()

      // add listeners for the zoom level and terrain and center
      google.maps.event.addListener(this.map, 'maptypeid_changed', this.setDropdownTerrain)
      google.maps.event.addListener(this.map, 'zoom_changed', this.setDropdownZoom)
      google.maps.event.addListener(this.map, 'dragend', this.setCenter)

      // if the zoom level or map type changes in the dropdown, the map needs to change
      $('#zoomLevel').bind('change', () => {
        this.setMapZoom($('#zoomLevel').val())
      })
      $('#mapType').bind('change', $.proxy(this.setMapTerrain, this))
      $('#mapStyle').bind('change', $.proxy(this.setMapStyle, this))
    }
  }

  /**
   * Add marker to the map
   *
   * @param object map
   * @param object bounds
   * @param object object
   */
  addMarker (map, bounds, object) {
    const position = new google.maps.LatLng(object.lat, object.lng)
    bounds.extend(position)

    // add marker
    const marker = new google.maps.Marker(
      {
        position: position,
        map: map,
        title: object.title
      })

    if (typeof object.dragable !== 'undefined' && object.dragable) {
      marker.setDraggable(true)

      // add event listener
      google.maps.event.addListener(marker, 'dragend', () => {
        this.updateMarker(marker)
      })
    }

    // add click event on marker
    google.maps.event.addListener(marker, 'click', function () {
      // create infowindow
      new google.maps.InfoWindow(
        {
          content: '<h1>' + object.title + '</h1>' + object.text
        }).open(map, marker)
    })
  }

  /**
   * Get map data
   */
  getMapData () {
    // get the live data
    this.zoomLevel = this.map.getZoom()
    this.type = this.map.getMapTypeId()
    this.style = this.getMapStyle()
    this.center = this.map.getCenter()
    this.centerLat = this.center.lat()
    this.centerLng = this.center.lng()

    // get the form data
    this.mapId = parseInt($('#mapId').val())
    this.height = parseInt($('#height').val())
    this.width = parseInt($('#width').val())

    this.showDirections = ($('#directions').attr('checked') === 'checked')
    this.showLink = ($('#fullUrl').attr('checked') === 'checked')
    this.showOverview = ($('#markerOverview').attr('checked') === 'checked')
  }

  /**
   * Panorama visibility changed
   */
  panoramaVisibilityChanged (e) {
    // panorama is now invisible
    if (!this.panorama.getVisible()) {
      // select default map type
      $('#mapType option:first-child').attr('selected', 'selected')

      // set map terrain
      this.setMapTerrain()
    }
  }

  /**
   * Refresh page refresh the page and display a certain message
   *
   * @param string message
   */
  /**
   * Get map style
   */
  getMapStyle () {
    return $('#mapStyle').find('option:selected').val()
  }

  // this will refresh the page and display a certain message
  refreshPage (message) {
    const currLocation = window.location
    let reloadLocation = (currLocation.search.indexOf('?') >= 0) ? '&' : '?'
    reloadLocation = currLocation + reloadLocation + 'report=' + message

    // cleanly redirect so we can display a message
    window.location = reloadLocation
  }

  setCenter () {
    this.getMapData()
    $('#centerLat').val(this.centerLat)
    $('#centerLng').val(this.centerLng)
  }

  /**
   * Set dropdown terrain will set the terrain type of the map to the dropdown
   */
  setDropdownTerrain () {
    this.getMapData()
    $('#mapType').val(this.type.toUpperCase())
  }

  /**
   * Set dropdown zoom will set the zoom level of the map to the dropdown
   */
  setDropdownZoom () {
    this.getMapData()
    $('#zoomLevel').val(this.zoomLevel)
  }

  // this will set the theme style of the map to the dropdown
  setMapStyle () {
    this.style = $('#mapStyle').val()
    this.map.setOptions({'styles': Stylers.styles[this.style]})
  }

  /**
   * Set map terrain will set the terrain type of the map to the dropdown
   */
  setMapTerrain () {
    // init previous type
    const previousType = this.type.toLowerCase()

    // redefine type
    this.type = $('#mapType').val().toLowerCase()

    // do something when we have street view
    if (previousType === 'street_view' || this.type === 'street_view') {
      // init showPanorama if not yet initialised
      if (this.panorama === null) {
        // init panorama street view
        this.showPanorama()
      }

      // toggle visiblity
      this.panorama.setVisible((this.type === 'street_view'))
    }

    // set map type
    this.map.setMapTypeId(this.type)
  }

  /**
   * Set map zoom will set the zoom level of the map to the dropdown
   *
   * @param int zoomlevel
   */
  setMapZoom (zoomlevel) {
    this.zoomLevel = zoomlevel

    // set zoom automatically, defined by points (if allowed)
    if (zoomlevel === 'auto') {
      this.map.fitBounds(this.bounds)
    } else {
      this.map.setZoom(parseInt(zoomlevel))
    }
  }

  /**
   * Show map
   */
  showMap () {
    // create boundaries
    this.bounds = new google.maps.LatLngBounds()

    // define type if not already set
    if (this.type === null) this.type = window.mapOptions.type

    // define options
    const options = {
      center: new google.maps.LatLng(window.mapOptions.center.lat, window.mapOptions.center.lng),
      mapTypeId: google.maps.MapTypeId[this.type],
      styles: Stylers.styles[window.mapOptions.style]
    }

    // create map
    this.map = new google.maps.Map(document.getElementById('map'), options)

    // we want street view
    if (this.type === 'STREET_VIEW') {
      this.showPanorama()
      this.panorama.setVisible(true)
    }

    // loop the markers
    for (const i in window.markers) {
      this.addMarker(
        this.map, this.bounds, window.markers[i]
      )
    }

    this.setMapZoom(window.mapOptions.zoom)
  }

  /**
   * Show panorama - adds panorama to the map
   */
  showPanorama () {
    // get street view data from map
    this.panorama = this.map.getStreetView()

    // define position
    this.panorama.setPosition(new google.maps.LatLng(window.mapOptions.center.lat, window.mapOptions.center.lng))

    // define heading (horizontal °) and pitch (vertical °)
    this.panorama.setPov({
      heading: 200,
      pitch: 8,
      zoom: 1
    })

    // bind event listeners (possible functions: pano_changed, position_changed, pov_changed, links_changed, visible_changed)
    google.maps.event.addListener(this.panorama, 'visible_changed', this.panoramaVisibilityChanged)
  }

  /**
   * Update marker will re-set the position of a marker
   *
   * @param object marker
   */
  updateMarker (marker) {
    this.getMapData()
    $('[data-role=center-lat]').val(marker.getPosition().lat())
    $('[data-role=center-lng]').val(marker.getPosition().lng())
  }
}
