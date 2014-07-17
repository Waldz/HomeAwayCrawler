<?php

error_reporting(E^ALL);
ob_start();
session_start();

set_time_limit(10);
require_once 'library/simple_html_dom.php';
require_once 'includes/record_searches.php';

function rel2abs($rel, $base)
{
	if(strpos($rel,"//")===0)
	{
		return $rel;
		//return "http:".$rel;
	}
	/* return if  already absolute URL */
	if  (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;
	/* queries and  anchors */
	if ($rel[0]=='#'  || $rel[0]=='?') return $base.$rel;
	/* parse base URL  and convert to local variables:
	 $scheme, $host,  $path */
	extract(parse_url($base));
	/* remove  non-directory element from path */
	$path = preg_replace('#/[^/]*$#',  '', $path);
	/* destroy path if  relative url points to root */
	if ($rel[0] ==  '/') $path = '';
	/* dirty absolute  URL */
	$abs =  "$host$path/$rel";
	/* replace '//' or  '/./' or '/foo/../' with '/' */
	$re =  array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
	for($n=1; $n>0;  $abs=preg_replace($re, '/', $abs, -1, $n)) {}
	/* absolute URL is  ready! */
	return  $scheme.'://'.$abs;
}

function getLocations($html) {

	$pattern = '/.*var unitJSON\s*=\s*(.*)/im';

	$scripts = $html->find('script');

	$phone_numbers = array();
	$locations = array();

	foreach($scripts as $script) {

		$script_content = $script->innertext;

		$json_str = preg_replace($pattern, "$1", $script_content);

		//error_log("JSON-Str: ".$json_str.", Script-Content: ".$script_content);
		
		if(!empty($json_str) && $json_str != $script_content) {

			$json_str = trim($json_str);

			$json_str = substr($json_str, 0, strlen($json_str)-1);
			
			$json_index = strpos($json_str, '{"property":{"videoUrls":');
			if($json_index !== FALSE) {
				$json_str = substr($json_str, $json_index);
			}

			error_log("JSON: ".$json_str);
			$json_arr = json_decode($json_str, true);
			error_log("Array: ".print_r($json_arr, true));
			$phone_numbers[] = $json_arr['property']['contact']['primaryPhone']['phoneNumber'];
			$location_arr = json_decode($json_arr['property']['googleLocationJson'], true);
			$locations[] = array('lat' => urldecode($location_arr['location'][0]['cLat']), 'lng' => urldecode($location_arr['location'][0]['cLong']));
		}

	}

	return $locations;
}

if($_SESSION['ses_user_id'] == '')
{
	$_SESSION['ses_detail'] = $_REQUEST['targeturl'];
	header("location:login_signup.php");
}
else
{
	$_SESSION['ses_detail'] = '';
}

$targeturl=$_REQUEST['targeturl'];
/*$feed_url='http://www.homeaway.com'.$targeturl;
//$feed_url.='/print_preview_frame?print_format=details';
error_log("Feed URL: ".$feed_url);

$ch = curl_init();
$timeout = 60;
curl_setopt($ch,CURLOPT_URL,$feed_url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$markup = curl_exec($ch);
//error_log("Mark-up: ".$markup);
curl_close($ch);*/

$apartment = get_apartment_by_detail_url($targeturl);
//echo "Apartment: ".print_r($apartment, true);
$markup = $apartment['markup'];
//file_put_contents("/Users/developer/Desktop/markup_db.html", $markup);

$html = str_get_html($markup, false, false, 'utf-8', false);
// $details_html = $html->find('body', 0)->outertext;
// error_log($details_html);
// exit;
//file_put_contents("/Users/developer/Desktop/markup_new.html", $html);

//exit;

$MAP_PARENT = '#property-map';
//$MAP_PARENT = '#propertyLocation';

foreach($html->find('head link') as $stylesheet) {
	if(empty($stylesheet->href))
		continue;
	$stylesheet->href = rel2abs($stylesheet->href, 'http://www.homeaway.com/');
}

foreach($html->find('head script') as $javascript) {
	if(empty($javascript->src))
		continue;
	$javascript->src = rel2abs($javascript->src, 'http://www.homeaway.com/');
}

$head_css_js = $html->find('head', 0)->innertext;
//error_log("CSS JS: ".$head_css_js);
//echo $html->find('section[class=details with_images]')->outertext;

require_once("includes/code_header.php");
require_once("includes/header.php");
?>

<div align="center">
<style>
.hidden_in_print{display:none;}
div.padded_solid_black_border.items.item_rows.top_spacer { display: none; }
div.problem {display: none;}
#listing_vitals div.details_info:nth-last-child(1) { display: none;}
div.details.no_padding div.details_info:nth-child(3) {display: none;}
div.in_this_building.big_separator { display: none;}
.icon-zoom-in:before { content: "+";}
.icon-caret-right:before { content: ">";}
.icon-caret-left:before { content: "<";}
.icon-zoom-out:before { content: "-"; }
div.right_two_fifths {position: relative;}
/*div#map {
	height: 300px;
	left: 0;
	position: absolute;
	top: 278px;
	width: 379px;
}*/
#property-map {height:600px !important;}
body {
	overflow-x: hidden;
}
div#header, div.pdp-cap.navbar.gt-navbar, div.container.hidden-phone, div#footer {
	display: none !important;
}
div.top-ad-banner, div.page-top-advert {display: none !important;}
</style>
</div> 
<?php

$html->find($MAP_PARENT, 0)->innertext = '';

foreach($html->find('body img') as $image) {
	if(empty($image->src))
		continue;
	$image->src = rel2abs($image->src, 'http://www.homeaway.com/');
}
foreach($html->find('body a') as $anchor) {
	if(empty($anchor->href))
		continue;
	//$anchor->href = "javascript: void(0)";
}
$details_html = $html->find('body', 0)->outertext;
$details_html = str_replace('HomeAway', 'FlatFindr', $details_html);
echo $details_html ;

//error_log($details_html);

/*$body_js = '';
foreach($html->find('body script') as $javascript) {
	if(!empty($javascript->src))
		$javascript->src = rel2abs($javascript->src, 'http://www.homeaway.com/');
	
	$body_js .= $javascript->outertext;
	error_log("Body-JS: ".$body_js);
}*/

?> 
<?php 	
	$locations = getLocations($html);
	error_log("Locations: ".print_r($locations, true));
?>	
<script src='https://api.tiles.mapbox.com/mapbox.js/v1.6.2/mapbox.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox.js/v1.6.2/mapbox.css' rel='stylesheet' />

<script type="text/javascript">
//$('<?php echo $MAP_PARENT; ?>').html('<div id="map"></div>');
var map = L.mapbox.map('property-map', 'examples.map-9ijuk24y');
L.control.layers({
    'Base Map': L.mapbox.tileLayer('examples.map-zgrqqx0w').addTo(map),
    'Grey Map': L.mapbox.tileLayer('examples.map-20v6611k')
}, {
    'Bike Stations': L.mapbox.tileLayer('examples.bike-locations'),
    'Bike Lanes': L.mapbox.tileLayer('examples.bike-lanes')
}).addTo(map);

function addLocationToMap() {
	var latitude = longitude = marker = 0;
	<?php foreach($locations as $location) { ?>
		latitude = <?php echo $location['lat']; ?>;
	    longitude = <?php echo $location['lng']; ?>;
	    console.log("Latitude: "+latitude+", Longitude: "+longitude);
	    marker = L.marker([latitude, longitude]).addTo(map);
	    map.setView([latitude, longitude], 15);
	<?php } ?>
}
</script>
<script type="text/javascript">
$(document).ready(function(){
	addLocationToMap();
})
</script>

<?php //save_detail($targeturl, addslashes($html)); 
	?>
	
<?php 
require_once("includes/footer.php");
?>	
