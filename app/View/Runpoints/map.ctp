<!-- File: /app/View/Runpoints/map.ctp -->
<script type="text/javascript">
	var box_extents = [
		[139.5455, 35.6865, 139.5465, 35.6875],
		[139.5455, 35.6875, 139.5465, 35.6885],
		[139.5455, 35.6885, 139.5465, 35.6895],
		[139.5465, 35.6895, 139.5475, 35.6905],
		[139.5455, 35.6895, 139.5465, 35.6905],
	];
	var map;

	function init(){
		map = new OpenLayers.Map('map');

		var boxes  = new OpenLayers.Layer.Vector( "Boxes" );

		var mapnik = new OpenLayers.Layer.OSM();
		mapnik.opacity = 0.3;
		map.addLayer(mapnik);

		var lonLat = new OpenLayers.LonLat(139.5455, 35.6895).transform(
			new OpenLayers.Projection("EPSG:4326"), 
			new OpenLayers.Projection("EPSG:900913")
		);
		map.setCenter(lonLat, 14);

		for (var i = 0; i < box_extents.length; i++) {
			ext = box_extents[i];
			bounds = OpenLayers.Bounds.fromArray(ext).transform(
				new OpenLayers.Projection("EPSG:4326"), 
				new OpenLayers.Projection("EPSG:900913")
			);
			box = new OpenLayers.Feature.Vector(bounds.toGeometry());
			boxes.addFeatures(box);
		}

		map.addLayer(boxes);
		map.addControl(new OpenLayers.Control.LayerSwitcher());
		var sf = new OpenLayers.Control.SelectFeature(boxes);
		map.addControl(sf);
		sf.activate();

		if (!map.getCenter()) {
			map.setCenter(new OpenLayers.LonLat(139.546, 35.689), 12);
		}

	}
	document.body.onload=init; 
</script>

<h1>Your Kingdom MAP</h1>
<div id="tags">
		box, vector, annotation, light
</div>

<p id="shortdesc">
	Demonstrate marker and box type annotations on a map.
</p>
		
<div id="map" style="width:100%; height:100%"></div>

<div id="docs"></div>


