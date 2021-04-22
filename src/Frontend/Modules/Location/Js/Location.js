import { Data } from '../../../Core/Js/Components/Data'
import {StringUtil} from '../../../../Backend/Core/Js/Components/StringUtil'

export class Location {
  constructor () {
    this.map = {}
    this.mapFullUrl = null
    this.directionsService = null
    this.directionsDisplay = null
    this.panorama = {}

    if ($('*[data-role=fork-map-container][data-map-id]').length > 0) {
      $('*[data-role=fork-map-container][data-map-id]').each(() => {
        google.maps.event.addDomListener(
          window,
          'load',
          this.initMap($(this).data('map-id'))
        )
      })
    }
  }

  // init the map
  initMap (id) {
    // define some variables we will need
    const suffix = (id === 'general') ? '' : '_' + id
    const mapId = id
    const mapStyle = (Data.get('Location.settings' + suffix + '.map_style'))

    // define coordinates
    const coordinates = new google.maps.LatLng(
      Data.get('Location.settings' + suffix + '.center.lat'),
      Data.get('Location.settings' + suffix + '.center.lng')
    )

    // build the options
    const options =
      {
        zoom: (Data.get('Location.settings' + suffix + '.zoom_level') === 'auto') ? 0 : parseInt(Data.get('Location.settings' + suffix + '.zoom_level')),
        center: coordinates,
        mapTypeId: google.maps.MapTypeId[Data.get('Location.settings' + suffix + '.map_type')],
        styles: window.MAPS_CONFIG[mapStyle]
      }

    // create map
    this.map[mapId] = new google.maps.Map(
      $('*[data-role=fork-map-container][data-map-id=' + id + ']')[0],
      options
    )

    // we want a streetview
    if (Data.get('Location.settings' + suffix + '.map_type') === 'STREET_VIEW') {
      // get street view data from map
      this.panorama[mapId] = this.map[mapId].getStreetView()

      // define position
      this.panorama[mapId].setPosition(coordinates)

      // define heading (horizontal °) and pitch (vertical °)
      this.panorama[mapId].setPov({
        heading: 200,
        pitch: 8
      })

      // show panorama
      this.panorama[mapId].setVisible(true)
    }

    // get the items
    const items = Data.get('Location.items' + suffix)

    // any items
    if (items.length > 0) {
      // loop items
      for (const i in items) {
        // add markers
        this.addMarker(mapId, items[i].id, items[i].lat, items[i].lng, items[i].title)
      }
    }

    // are directions enabled?
    if (Data.get('Location.settings' + suffix + '.directions')) {
      // create direction variables if needed
      if (this.directionsService === null) this.directionsService = new google.maps.DirectionsService()
      if (this.directionsDisplay === null) this.directionsDisplay = new google.maps.DirectionsRenderer()

      // bind events
      $('form[data-role=fork-directions-form][data-map-id=' + id + ']').on('submit', function (e) {
        e.preventDefault()
        this.setRoute(id, mapId, items[0])
      })
    }

    if ($('a[data-role=fork-map-url][data-map-id=' + id + ']').length > 0) {
      this.mapFullUrl = $('a[data-role=fork-map-url][data-map-id=' + id + ']').attr('href')
    }
  }

  // add a marker
  addMarker (mapId, id, lat, lng, title) {
    // add the marker
    const marker = new google.maps.Marker(
      {
        position: new google.maps.LatLng(lat, lng),
        map: this.map[mapId],
        title: StringUtil.htmlEncode(title),
        locationId: id
      }
    )

    // show info window on click
    google.maps.event.addListener(marker, 'click', function () {
      const $markerText = $('*[data-role=fork-marker-data-container][data-map-id=' + marker.locationId + ']')
      let text = ''

      // apparently JS goes bananas with multi line HTMl, so we grab it from the div, this seems like a good idea for SEO
      if ($markerText.length > 0) text = $markerText.html()

      let content = '<h1>' + StringUtil.htmlEncode(title) + '</h1>'
      if (typeof text !== 'undefined') content += text

      new google.maps.InfoWindow(
        {
          content: content
        }
      ).open(this.map[mapId], marker)
    })
  }

  // calculate the route
  setRoute (id, mapId, item) {
    const $error = $('*[data-role=fork-directions-error][data-map-id=' + id + ']')
    const $search = $('*[data-role=fork-directions-start][data-map-id=' + id + ']')

    // validate
    if ($search.val() === '') {
      $error.show()
    } else {
      $error.hide()
    }

    // build the position
    const position = new google.maps.LatLng(item.lat, item.lng)

    // build request
    const request =
      {
        origin: $search.val(),
        destination: position,
        travelMode: google.maps.DirectionsTravelMode.DRIVING
      }

    // request the route
    this.directionsService.route(request, (response, status) => {
      // did we find a route
      if (status === google.maps.DirectionsStatus.OK) {
        // change the map
        this.directionsDisplay.setMap(this.map[mapId])

        // render the route
        this.directionsDisplay.setDirections(response)

        // change the link
        if (this.mapFullUrl !== null) {
          // get "a"-link element
          const $item = $('a[data-role=fork-map-url][data-map-id=' + id + ']')

          // d = directions
          const href = this.mapFullUrl + '&f=d&saddr=' + $search.val() + '&daddr=' + position

          // update href
          $item.attr('href', href)
        }
      } else {
        $error.show()
      }
    })
  }
}
