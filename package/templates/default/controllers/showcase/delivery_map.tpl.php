<script type="text/javascript">
	<?php if ($provider == 'yandex'){ ?>
		var myMap, myPlacemark, coords;
		ymaps.ready(init);

		function init() {
			myMap = new ymaps.Map('select_delivery_map', {
				center: [<?php echo $value; ?>], 
				zoom: 10,
				type: 'yandex#map',
				controls: ['zoomControl', 'typeSelector',  'fullscreenControl']
			});
			
			<?php if ($pickup){ ?>
				myPlacemark = new ymaps.Placemark([<?php echo $value; ?>], {
					balloonContentHeader: "Цена: <?php echo $pickup['price'] . ' ' . (!empty($this->controller->options['cerrency']) ? $this->controller->options['cerrency'] : LANG_CURRENCY); ?>",
					balloonContentBody: "<?php echo addslashes($pickup['title']); ?>",
					balloonContentFooter: "<?php html($pickup['pickup_address']); ?>",
					hintContent: "Здесь"
				}, {
					balloonPanelMaxMapArea: 0,
					draggable: "true",
					preset: "islands#blueStretchyIcon",
					openEmptyBalloon: false
				});
				myMap.geoObjects.add(myPlacemark);
			<?php } else { ?>
			
				coords = "<?php html($value); ?>";

				myPlacemark = new ymaps.Placemark([<?php echo $value; ?>], {
					hintContent: 'Перетащите метку'
				}, {
					balloonPanelMaxMapArea: 0,
					draggable: "true",
					preset: "islands#blueStretchyIcon",
					openEmptyBalloon: false
				});
				
				myMap.geoObjects.add(myPlacemark);	
				
				myPlacemark.events.add("dragend", function (e) {
					coords = this.geometry.getCoordinates();
					savecoordinats();
				}, myPlacemark);
				
				myMap.events.add('click', function (e) {        
					coords = e.get('coords');
					myPlacemark.geometry.setCoordinates(coords);				
					savecoordinats();
				});	
				
				myMap.events.add('boundschange', function (event) {
					if (typeof coords === "function") {
						if (event.get('newZoom') != event.get('oldZoom')) { savecoordinats(); }
						if (event.get('newCenter') != event.get('oldCenter')) { savecoordinats(); }
					}
				});
			
			<?php } ?>

		}
	<?php } else { ?>
			geocoder = new google.maps.Geocoder();
			var center = new google.maps.LatLng(<?php echo $value; ?>);
			 
			var map = new google.maps.Map(document.getElementById('select_delivery_map'), {
			   zoom: 10,
			   center: center,
			   disableDefaultUI: true,
			   zoomControl: true,
			   mapTypeControl: true,
			});
			
			<?php if ($pickup){ ?>
				var beachMarker = new google.maps.Marker({
					position: center,
					map: map
				});
				var content = '<div style="color:#000"><strong>Цена: <?php echo addslashes($pickup['price'] . ' ' . (!empty($this->controller->options['cerrency']) ? $this->controller->options['cerrency'] : LANG_CURRENCY)); ?></strong></div><div style="color:#333"><?php html($pickup['title']); ?></div><div style="color:#333"><?php html($pickup['pickup_address']); ?></div>';
				var infowindow = new google.maps.InfoWindow({
					content: content
				});
				google.maps.event.addListener(beachMarker, 'click', function() {
					infowindow.open(map, beachMarker);
				});
			<?php } else { ?>
				var marker = new google.maps.Marker({
					position: center,
					draggable: true,
					map: map
				});
				google.maps.event.addListener(map, 'maptypeid_changed', function() {mapType = map.getMapTypeId();});
				google.maps.event.addListener(marker, "dragend", function () { 
					var coords = marker.getPosition();
					savecoordinats(coords.lat(), coords.lng());
				});
				
				google.maps.event.addListener(map, 'click', function(e) {
					latlng = {lat: e.latLng.lat(),lng: e.latLng.lng()};
					marker.setPosition(latlng);
					savecoordinats(e.latLng.lat(), e.latLng.lng());
				});
			<?php } ?>
	<?php } ?>
	
	<?php if (!$pickup){ ?>
		function savecoordinats(lat, lng){
			<?php if ($provider == 'google'){ ?>
				new_coords = lat + ", " + lng;
			<?php } else { ?>
				var new_coords = [coords[0].toFixed(4), coords[1].toFixed(4)];
				myPlacemark.geometry.setCoordinates(new_coords);
			<?php } ?>
			$('#saveCordBtn').data('coord', new_coords);
		}
		
		function cordToInput(button){
			var new_coords = $(button).data('coord') ? $(button).data('coord') : false;
			if (new_coords){
				document.getElementById("<?php html($field_or_id); ?>").value = new_coords;
				$('#geo-widget-<?php html($field_or_id); ?> span').text(new_coords).show();
				icms.modal.close();
			} else {
				document.getElementById("<?php html($field_or_id); ?>").value = '<?php html($value); ?>';
				$('#geo-widget-<?php html($field_or_id); ?> span').text('<?php html($value); ?>').show();
				icms.modal.close();
			}
		}
	<?php } ?>
</script>
<div id="select_delivery_map" style="height:500px;width:500px"></div>
<?php if (!$pickup){ ?>
	<div class="buttons" style="padding:10px">
		<input type="button" value="<?php echo LANG_SAVE; ?>" class="button-submit" id="saveCordBtn" onClick="cordToInput(this)">
	</div>
<?php } ?>