<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
require_once JPATH_COMPONENT.'/helpers/christianconnect.php';

// load tooltip behavior
JHtml::_('behavior.tooltip');
count($this->items); 

?>

<form action="<?php echo JRoute::_('index.php?option=com_christianconnect&view=findchurch'); ?>" method="post" name="adminForm" id="adminForm">  
    <div class="searchdrop">
    <select name="category" >
			<?php echo JHtml::_('select.options',ChristianconnectHelper::getCategoryOptions(), 'value', 'text', $this->state->get('search.category'));?>
	</select>
    <!--<select name="country" >
			<?php /*echo JHtml::_('select.options',ChristianconnectHelper::getCountryOptions(), 'value', 'text', $this->state->get('search.country'));*/?>
	</select>
	-->
    <input class="button" type="submit" name="submit" value="find church"></input>
    </div>
    
	<div id='map_canvas'></div>
  </form>
  <div class="legend">
  <ul>
 <?php foreach($this->legends as $legend){?>
	<li><?php echo $legend->title; ?> <img src="<?php echo JURI::base().$legend->map_pin?>"></img></li>
<?php }?> 
	</ul>
</div>
<?php if(count($this->items)!=0){?>
<script src="http://www.google.com/jsapi"></script> 
<script type="text/javascript"> 
google.load("maps", "3", {other_params:"sensor=false"});
</script> 

<script type='text/javascript'>
//<![CDATA[
var map;
var markers = [];
var infoWindow;
 
	    var bounds = new google.maps.LatLngBounds();
		var infoWindow = new google.maps.InfoWindow({disableAutoPan:true,pane:6,});
		var map = new google.maps.Map(document.getElementById("map_canvas"), {
		  center: new google.maps.LatLng(40, -100),
		  zoom: 4,
		  mapTypeId: 'roadmap',
		  mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
		
		});
 


 function clearLocations() {
   //infoWindow.close();
   for (var i = 0; i < markers.length; i++) {
     markers[i].setMap(null);
   }
   markers.length = 0;
 }

function createMarker(latlng,marker, map) {
    google.maps.event.addListener(marker, 'click', function() {
    	 window.location.href = marker.url;
    });
    markers.push(marker);
  }
map.setZoom(6);
map.fitBounds(bounds);

 function doNothing() {}
      
     //]]>
     
       </script>
 
<?php 
	foreach($this->items as $item){
			 
?>
 			<script type="text/javascript">
                            // Change this depending on the name of your PHP file
                               var point = new google.maps.LatLng(
               		              parseFloat(<?php echo $item->lat;?>),
               		              parseFloat(<?php echo $item->lng;?>));
                              	var marker = new google.maps.Marker({
                                 map: map,
                                // url: '<?php echo JRoute::_('index.php?option=com_christianconnect&task=mychurch.getfulldetail&churchid='.$item->id); ?>',
	               		         position: point,
                                 icon: "<?php echo JURI::base();?><?php echo $item->pin;?>"
                               });
                               	createMarker(point,marker, map);
                                bounds.extend(point);
              </script>
               		    <?php }?>
<div class="searchresult">
<table align="center">
<tr><td><span class="name">Name</span></td><td><span class="address">Address</span></td></tr>
	<?php 
		$i=1;
		foreach($this->items as $item){?>
			<tr>
			
			<td>
<!--			 <a class="pagenav" href="<?php echo JRoute::_('index.php?option=com_christianconnect&task=mychurch.getfulldetail&churchid='.$item->id); ?>">-->
			 <?php echo $item->cname;?> 
<!--			 </a>-->
			 </td>
			 <td>
			 <?php  echo $item->address1.$item->address2.$item->city.$item->state.$item->country; ?>
			  </td></tr>
	
   <?php $i++; }?>
   </table>
</div>
           			
<div class="pagination searchpage">
	<p class="counter">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
		
<?php }else{?>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
	<style>
      #map {
        height: 400px;
        width: 600px;
        border: 1px solid #333;
        margin-top: 0.6em;
      }
    </style>
    <script>
      var map;
      var infowindow;

      function initialize() {
        var requestlocation = new google.maps.LatLng(<?php echo $this->location->lat;?>, <?php echo $this->location->lng;?>);
       // var mapcenter = new google.maps.LatLng(40, -100);

        map = new google.maps.Map(document.getElementById('map_canvas'), {
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          center: requestlocation,
          mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
          zoom: 6
        });

        var request = {
          location: requestlocation,
          radius: 50000,
          types: ['church']
        };
        infowindow = new google.maps.InfoWindow();
        var service = new google.maps.places.PlacesService(map);
        service.nearbySearch(request, callback);
      }

      function callback(results, status) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
          for (var i = 0; i < results.length; i++) {
            createMarker(results[i]);
          }
        }
      }

      function createMarker(place) {
        var placeLoc = place.geometry.location;
        var marker = new google.maps.Marker({
          map: map,
          position: place.geometry.location
        });

        google.maps.event.addListener(marker, 'click', function() {
          infowindow.setContent(place.name);
          infowindow.open(map, this);
        });
       
      }
      google.maps.event.addDomListener(window, 'load', initialize);
      
    </script>
	
<?php }?>

