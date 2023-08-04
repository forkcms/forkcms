export const Stylers = {
  styles: [
    {
      standard: [],
      custom: [],
      // GRAY STYLE
      gray: [
        // BASIC
        {
          stylers: [
            {hue: '#B9B9B9'},
            {saturation: -100}
          ]
        },
        // Lanscape
        {
          featureType: 'landscape',
          stylers: [
            {
              color: '#E5E5E5'
            }
          ]
        },
        // Water
        {
          featureType: 'water',
          stylers: [
            {
              'visibility': 'on'
            },
            {
              color: '#DCDCDC'
            }
          ]
        },
        // Transit
        {
          featureType: 'transit',
          elementType: 'geometry',
          stylers: [
            {
              color: '#B9B9B9'
            }
          ]
        },
        // Road hight way
        {
          featureType: 'road.highway',
          elementType: 'geometry',
          stylers: [
            {
              color: '#E2E2E2'
            }
          ]
        },
        // Road hight way control access
        {
          featureType: 'road.highway.controlled_access',
          elementType: 'geometry',
          stylers: [
            {
              color: '#ACACAC'
            }
          ]
        },
        // Road arterial
        {
          featureType: 'road.arterial',
          elementType: 'geometry',
          stylers: [
            {
              color: '#ffffff'
            }
          ]
        },
        // Road local
        {
          featureType: 'road.local',
          elementType: 'geometry',
          stylers: [
            {
              color: '#F6F6F6'
            }
          ]
        },
        // Point global
        {
          featureType: 'poi',
          elementType: 'geometry',
          stylers: [
            {
              color: '#DDDDDD'
            }
          ]
        },
        {
          featureType: 'poi.park',
          elementType: 'geometry',
          stylers: [
            {
              color: '#D3D3D3'
            }
          ]
        },
        {
          featureType: 'poi.attraction',
          elementType: 'geometry',
          stylers: [
            {
              color: '#DBDBDB'
            }
          ]
        },
        {
          featureType: 'poi.business',
          elementType: 'geometry',
          stylers: [
            {
              color: '#DBDBDB'
            }
          ]
        },
        {
          featureType: 'poi.place_of_worship',
          elementType: 'geometry',
          stylers: [
            {
              color: '#DBDBDB'
            }
          ]
        }
      ],
      // Blue STYLE
      blue: [
        // BASIC
        {
          stylers: [
            {hue: '#4C95B2'},
            {saturation: -100}
          ]
        },
        // Lanscape
        {
          featureType: 'landscape',
          stylers: [
            {
              color: '#DEE7EB'
            }
          ]
        },
        // Water
        {
          featureType: 'water',
          stylers: [
            {
              'visibility': 'on'
            },
            {
              color: '#BBE7F9'
            }
          ]
        },
        // Transit
        {
          featureType: 'transit',
          elementType: 'geometry',
          stylers: [
            {
              color: '#86CEEB'
            }
          ]
        },
        // Road hight way
        {
          featureType: 'road.highway',
          elementType: 'geometry',
          stylers: [
            {
              color: '#C2EDFF'
            }
          ]
        },
        // Road hight way control access
        {
          featureType: 'road.highway.controlled_access',
          elementType: 'geometry',
          stylers: [
            {
              color: '#4DCDFF'
            }
          ]
        },
        // Road arterial
        {
          featureType: 'road.arterial',
          elementType: 'geometry',
          stylers: [
            {
              color: '#ffffff'
            }
          ]
        },
        // Road local
        {
          featureType: 'road.local',
          elementType: 'geometry',
          stylers: [
            {
              color: '#F3F7F9'
            }
          ]
        },
        // Point global
        {
          featureType: 'poi',
          elementType: 'geometry',
          stylers: [
            {
              color: '#C6E5F1'
            }
          ]
        },
        {
          featureType: 'poi.park',
          elementType: 'geometry',
          stylers: [
            {
              color: '#B7DDEC'
            }
          ]
        },
        {
          featureType: 'poi.attraction',
          elementType: 'geometry',
          stylers: [
            {
              color: '#D5DDE0'
            }
          ]
        },
        {
          featureType: 'poi.business',
          elementType: 'geometry',
          stylers: [
            {
              color: '#D5DDE0'
            }
          ]
        },
        {
          featureType: 'poi.place_of_worship',
          elementType: 'geometry',
          stylers: [
            {
              color: '#D5DDE0'
            }
          ]
        }
      ]
    }
  ]
}
