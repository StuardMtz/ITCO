<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Mapa de Calor</title>
    <style>
        #map {
            height: 100%;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #floating-panel {
            position: absolute;
            top: 10px;
            left: 25%;
            z-index: 5;
            background-color: #fff;
            padding: 5px;
            border: 1px solid #999;
            text-align: center;
            font-family: 'Roboto', 'sans-serif';
            line-height: 30px;
            padding-left: 10px;
        }

        #floating-panel {
            background-color: #fff;
            border: 1px solid #999;
            left: 25%;
            padding: 5px;
            position: absolute;
            top: 10px;
            z-index: 5;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
</head>

<body>
    <div id="map"></div>
    <script>
        var map, heatmap;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 7,
                center: {
                    lat: 14.616166400000001,
                    lng: -90.5510912
                },
                mapTypeId: 'roadmap'
            });

            var puntos = [];
            var infowindow = [];
            var coords = <?php echo json_encode($lat); ?>;
            for (var i = 0; i < coords.length; i++) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(coords[i].lat, coords[i].lng),
                    map: map,
                    title: coords[i].dire
                });
                var contentString = 'Dirección' + coords[i].dire;
                var infowindow = new google.maps.InfoWindow({
                    content: contentString,
                    maxWidth: 160
                });


                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        // close all the other infowindows that opened on load
                        google.maps.event.trigger(map, 'click')
                        var contentString = 'Dirección ' + coords[i].dire;
                        infowindow.setContent(contentString);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            }




            var Data = puntos;

            pointArray = new google.maps.MVCArray(Data);

            heatmap = new google.maps.visualization.HeatmapLayer({
                data: pointArray,
                dissipating: true,
                map: map
            });
        }
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1rD0VqJ_0Nb8_lMeFV9aMEpWg-Jliq88&libraries=visualization&callback=initMap">
    </script>
</body>

</html>
