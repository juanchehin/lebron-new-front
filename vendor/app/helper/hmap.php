<?php

class HMap
{
    private $latlong;
    private $direccion;
    private $hide_map;

    public function setLatLong($value)
    {
        $this->latlong = $value;
        return;
    }

    public function setDireccion($value)
    {
        $this->direccion = $value;
        return;
    }

    public function setHideMap()
    {
        $this->hide_map = true;
        return;
    }

    public function drawMap()
    {
        ob_start();
        ?>
        <style type="text/css">
            #map {
                height: 318px;
                border: 1px solid #ccc;
                display: <?= ( $this->hide_map ? "none" : "") ?>
            }
        </style>
        <div id="group-location">
            <input type="text" name="ubicacion" class="form-control" id="ubicacion" value="<?= $this->direccion ?>">
        </div>
        <input type="hidden" id="latlong" name="latlong" value="<?= $this->latlong ?>">
        <div id="map"></div>
        <script type="text/javascript">
            var input = document.getElementById('ubicacion');
            function initAutocomplete()
            {
                var lat_long = "<?=$this->latlong ?: "-26.7636022,-65.28293589999998"?>";
                var center = lat_long.split(",");
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {"lat": parseFloat(center[0]), "lng": parseFloat(center[1])}, //new google.maps.LatLng(parseFloat(center[0]), parseFloat(center[1])),
                    zoom: lat_long ? 15 : 8,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    scrollwheel: false
                });
                var markers = [];
                var searchBox;

                markers.push(new google.maps.Marker({
                    map: map,
                    title: (input !== null) ? input.value : '<?=$this->direccion?>',
                    position: map.center
                }));

                if ( input )
                {
                    searchBox = new google.maps.places.SearchBox(input);
                    var inputLatLng = document.getElementById('latlong');
                    //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                    map.addListener('bounds_changed', function () {
                        searchBox.setBounds(map.getBounds());
                    });

                    searchBox.addListener('places_changed', function () {
                        var places = searchBox.getPlaces();

                        if ( places.length === 0 )
                        {
                            return;
                        }
                        markers.forEach(function (marker) {
                            marker.setMap(null);
                        });
                        markers = [];
                        var bounds = new google.maps.LatLngBounds();
                        places.forEach(function (place) {
                            var marker = new google.maps.Marker({
                                map: map,
                                title: place.name,
                                position: place.geometry.location,
                                draggable: true
                            });
                            //openInfoWindow(marker)
                            markers.push(marker);
                            google.maps.event.addListener(marker, 'dragend', function () {
                                var markerLatLng = marker.getPosition();
                                inputLatLng.value = markerLatLng.lat() + "," + markerLatLng.lng();
                            });

                            if ( place.geometry.viewport )
                            {
                                bounds.union(place.geometry.viewport);
                            }
                            else
                            {
                                bounds.extend(place.geometry.location);
                            }
                        });
                        map.fitBounds(bounds);
                        inputLatLng.value = map.center.lat() + "," + map.center.lng();
                    });
                }
            }

            function openInfoWindow(marker)
            {
                var markerLatLng = marker.getPosition();
                infoWindow.setContent([
                    '&lt;b&gt;La posicion del marcador es:&lt;/b&gt;&lt;br/&gt;',
                    markerLatLng.lat(),
                    ', ',
                    markerLatLng.lng(),
                    '&lt;br/&gt;&lt;br/&gt;Arr&amp;aacute;strame y haz click para actualizar la posici&amp;oacute;n.'
                ].join(''));
                infoWindow.open(map, marker);
            }
        </script>
        <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyADhmnaKgUVP0l63UFkiKcYYYXssvFZDJI&amp;libraries=places&amp;callback=initAutocomplete"></script>
        <?php
        return ob_get_clean();
    }
}