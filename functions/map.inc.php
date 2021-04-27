<?php
/**
 * Map Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Map Builder
 *
 * @param string $map_div_id Map DIV ID
 * @param array $position_array Position (latitude,longitude)
 * @param array $marker_array Maker (latitude,longitude,label)
 * @param string $modal_id Modal windows ID
 * @return boolean
 */
function api_map_builder($map_div_id,$position_array,$marker_array=null,$modal_id=null){
	// check parameters
	if(!is_array($position_array)){return false;}
	if(!is_array($marker_array)){$marker_array=array();}
	// check for marker
	if(count($marker_array)){
		$marker_position=$marker_array[0].",".$marker_array[1];
		$marker_label=$marker_array[2];
	}
	// build map script
	$map_script="function loadMap(){\n";
	$map_script.=" var latlng=L.latLng(".implode(",",$position_array).");\n";
	$map_script.=" var mymap = L.map('".$map_div_id."').setView(latlng,14);\n";
	$map_script.=" L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:\"&copy; <a href='https://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors\",}).addTo(mymap);\n";
	// check for marker
	if(count($marker_array)){
		$map_script.=" var latlng=L.latLng(".$marker_position.");\n";
		if($marker_label){$map_script.=" L.marker(latlng).addTo(mymap).bindPopup(\"".$marker_label."\");\n";}
		else{$map_script.=" L.marker(latlng).addTo(mymap);\n";}
	}
	$map_script.="}\n";
	// check for modal
	if($modal_id){
		$map_script.="// Load map after modal show\n";
		$map_script.="$('#".$modal_id."').on('shown.bs.modal',function(){loadMap();});\n";
	}
	/*setTimeout(function(){map.invalidateSize();},10);*/
	// Leaf Let CSS
	$GLOBALS['app']->addStylesheet(PATH."helpers/leaflet/css/leaflet-1.3.4.css");
	// jQuery scripts
	$GLOBALS['app']->addScript(PATH."helpers/leaflet/js/leaflet-1.3.4.min.js",true);
	$GLOBALS['app']->addScript($map_script);
	// return
	return true;
}
