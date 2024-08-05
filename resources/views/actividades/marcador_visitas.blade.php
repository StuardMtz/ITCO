<script>
  var url_global='{{url("/")}}';
</script>
<style>
    #map {
      height: 100%;
    }
  </style>

<div class="container-fluid">
  <div id="map"></div>
</div>

<script>
var map, heatmap;
function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 7,
    center: {lat: 14.616166400000001, lng: -90.5510912},
    mapTypeId: 'roadmap'
  });
  const icono_hotel = {
    url: url_global+'/storage/hotel.png',
    // This marker is 20 pixels wide by 32 pixels high.
    size: new google.maps.Size(50, 45),
    // The origin for this image is (0, 0).
    origin: new google.maps.Point(0, 0),
    // The anchor for this image is the base of the flagpole at (0, 32).
    anchor: new google.maps.Point(0, 40),
  };
  const icono_comida = {
    url: url_global+'/storage/comida.png',
    // This marker is 20 pixels wide by 32 pixels high.
    size: new google.maps.Size(50, 50),
    // The origin for this image is (0, 0).
    origin: new google.maps.Point(0, 0),
    // The anchor for this image is the base of the flagpole at (0, 32).
    anchor: new google.maps.Point(0, 32),
  };
  var infowindow = [];
  var coords = <?php echo json_encode($lat); ?>;
  for (var i = 0; i < coords.length; i++) {
    marker = new google.maps.Marker({
      position: new google.maps.LatLng(coords[i].lat, coords[i].lng),
      map: map,
      icon: url_global+'/'+`${coords[i].icono}`,
      title: `${coords[i].orden}. ${coords[i].dire}`,
      label: `${coords[i].orden}`,
    });
    var contentString = 'Descripcion' + coords[i].dire;
    var infowindow = new google.maps.InfoWindow({
      content: contentString,
      maxWidth: 160
    });  
    google.maps.event.addListener(marker, 'click', (function(marker, i) {
      return function() {
        // close all the other infowindows that opened on load
        google.maps.event.trigger(map, 'click')
        var contentString ='<li><b>Número: '+ `${coords[i].orden}`+'</b></li>'+'<li><b>Descripción: </b>'+ coords[i].dire+'</li>'
        +'<li><b>Prospecto: </b>'+coords[i].prospecto+'</li>'+'<li><b>Vendedor: </b>'+coords[i].vendedor+'</li>';
        infowindow.setContent(contentString);
        infowindow.open(map, marker);
      }
    })(marker, i));
  }
  var coordss = <?php echo json_encode($lats); ?>;
  for (var i = 0; i < coordss.length; i++) {
    marker = new google.maps.Marker({
      position: new google.maps.LatLng(coordss[i].lat, coordss[i].lng),
      map: map,
      icon: icono_hotel,
      title: `${i + 1}. ${coordss[i].dire}`,
      label: `${i + 1}`,
    });
    var contentString = 'Descripcion' + coordss[i].dire;
    var infowindow = new google.maps.InfoWindow({
      content: contentString,
      maxWidth: 160
    });  
    google.maps.event.addListener(marker, 'click', (function(marker, i) {
      return function() {
        // close all the other infowindows that opened on load
        google.maps.event.trigger(map, 'click')
        var contentString = '<li><b>Descripción: </b>'+ coordss[i].dire+'</li>'
        +'<li><b>Prospecto: </b>'+coordss[i].prospecto+'</li>'+'<li><b>Vendedor: </b>'+coordss[i].vendedor+'</li>';
        infowindow.setContent(contentString);
        infowindow.open(map, marker);
      }
    })(marker, i));
  }
  var comidacord = <?php echo json_encode($comlat); ?>;
  for (var i = 0; i < comidacord.length; i++) {
    marker = new google.maps.Marker({
      position: new google.maps.LatLng(comidacord[i].lat, comidacord[i].lng),
      map: map,
      icon: icono_comida,
      title: `${i + 1}. ${comidacord[i].dire}`,
      label: `${i + 1}`,
    });
    var contentString = 'Descripcion' + comidacord[i].dire;
    var infowindow = new google.maps.InfoWindow({
      content: contentString,
      maxWidth: 160
    });  
    google.maps.event.addListener(marker, 'click', (function(marker, i) {
      return function() {
        // close all the other infowindows that opened on load
        google.maps.event.trigger(map, 'click')
        var contentString = '<li><b>Descripción: </b>'+ comidacord[i].dire+'</li>'
        +'<li><b>Prospecto: </b>'+comidacord[i].prospecto+'</li>'+'<li><b>Vendedor: </b>'+comidacord[i].vendedor+'</li>';
        infowindow.setContent(contentString);
        infowindow.open(map, marker);
      }
    })(marker, i));
  }  
}    
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1rD0VqJ_0Nb8_lMeFV9aMEpWg-Jliq88&libraries=visualization&callback=initMap"></script>
