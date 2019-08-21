import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

export default class Map {

    static init () {
        let map = document.querySelector('#map')
        if (map === null) {
            return
        }
        let icon = L.icon({
            iconUrl: '../images/marker-icon-2x.png',
            iconSize:     [20, 30],
            // shadowSize:   [50, 64],
            // iconAnchor:   [22, 94],
            // shadowAnchor: [4, 62],
            // popupAnchor:  [-3, -76]
        })
        let center = [map.dataset.lat, map.dataset.lng]
        map = L.map('map').setView(center, 13)
        let token = 'pk.eyJ1IjoiZ2FpbSIsImEiOiJjanZ3aDdwajgwM280NGFtcXJ4aHJycXRzIn0.-EmSgKCnRAn5aGRqBWqlSA'
        L.tileLayer(`https://api.mapbox.com/v4/mapbox.streets/{z}/{x}/{y}.png?access_token=${token}`, {
            maxZoom: 18,
            minZoom: 12,
            detectRetina: true,
            attribution: '© <a href="https://www.mapbox.com/feedback/">Mapbox</a> © <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map)
        L.marker(center, {icon: icon}).addTo(map)
            // .bindPopup('A pretty CSS3 popup.<br> Easily customizable.')
            // .openPopup();
    }

}
