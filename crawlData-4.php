<?php
include ('Dom/ResultSet.php');
include ('Dom/Scrape.php');
include ('Dom/Result.php');
include ('library/simple_html_dom.php');
include ('includes/record_searches.php');

// http://www.homeaway.com/search/keywords:Family/arrival:2014-04-24/departure:2014-04-25/minSleeps/3/page:2
$feed_url = "http://www.homeaway.com/search/";

if (isset ( $_POST ['do_search'] ) || 1 == 1 || $_GET ['submit'] == 1) {
	if ($_GET ['submit'] == 1) {
		$keywords = urldecode($_GET ['keywords']);
		$startDate = $_GET['startDateInput'];
		$endDate = $_GET ['endDateInput'];
		$sleeps = $_GET ['sleepsInput'];
		$page = $_GET ['page'];
		$sort = $_GET ['sort_by'];
	} else {
		$keywords = urldecode($_POST ['keywords']);
		$startDate = $_POST['startDateInput'];
		$endDate = $_POST ['endDateInput'];
		$sleeps = $_POST ['sleepsInput'];
		$page = $_POST ['page'];
		$sort = $_POST ['sort_by'];
	}
	
	if(!empty($keywords)) {
		
		$feed_url = $feed_url . "keywords:{$keywords}/";
	}
	
	if (!empty($startDate)) {
		
		$feed_url = $feed_url . "arrival:{$startDate}/";
	}
	
	if (!empty($endDate)) {
		
		$feed_url = $feed_url . "departure:{$endDate}/";
	}
	
	if (!empty($sleeps)) {
		
		$feed_url = $feed_url . "minSleeps/{$sleeps}/";
	}
}

$is_next_enabled = true;
$is_prev_enabled = true;
$pagination_text = '';

if(empty($page))
	$page = 1;
$resultSet = search_apartments($keywords, $sleeps, $page);
if($page < 2)
	$is_prev_enabled = false;
if(count($resultSet->getResults()) < 30) 
	$is_next_enabled = false;

// if (! isset ( $page ) && $page == "") {
// 	$page = '1';
// }
// $feed_url = $feed_url . "page:{$page}/";

// $resultSet = new Property_Dom_ResultSet ();

// error_log("Feed URL: ".$feed_url);
// $markup = file_get_contents ( $feed_url );

// $html = str_get_html($markup);


// $img_urls = array();
// $i = 0;
// foreach($html->find('div.hit-collection-view div.listing-row') as $item) {
	
// 	foreach ( $item->find('div.listing-faces div.listing-face div.listing-face-content div.content-container div.listing-column-content div.listing-column-details-rates div.listing-title-container h3.listing-title') as $heading ) {
// 		$result = new Property_Dom_Result ();
// 		$result->type = 'PAID';
// 		$result->position = $i + 1;
// 		$resultSet->addResult ( $result );
// 		error_log("Heading: ".$heading->plaintext);
// 		$resultSet->addTitleByIndex ( $i, html_entity_decode($heading->plaintext) );
// 	}
	
// 	foreach ( $item->find('div.listing-faces div.listing-face div.listing-face-content div.content-container div.listing-column-content div.listing-column-details-rates div.listing-description div.listing-beds-baths') as $description ) {
// 		error_log("Description: ".$description->plaintext);
// 		$resultSet->addDescriptionByIndex ( $i, html_entity_decode($description->plaintext) );
// 	}
	
// 	foreach ( $item->find('div.listing-faces div.listing-face div.listing-face-content div.content-container div.listing-column-content div.listing-column-details-rates div.listing-description div.property-text ol') as $description2 ) {
// 		$str_text = html_entity_decode($description2->plaintext);
// 		$str_text = preg_replace( '/[^[:print:]]/', '',$str_text);
		
// 		foreach($item->find('div.listing-faces div.listing-face div.listing-face-content div.content-container div.listing-column-content div.listing-column-details-rates div.listing-description div.property-text span') as $span_elem) {
// 			$desc_number_text = $span_elem->plaintext;
// 		}
		
// 		$str_text .= ' ' . $desc_number_text;
// 		error_log("Description2: ".$str_text);
// 		$resultSet->addDescriptionByIndex ( $i, $str_text );
// 	}
	
// 	foreach ( $item->find('div.listing-faces div.listing-face div.listing-face-content div.content-container div.listing-column-photo div.listing-img') as $image ) {
// 		$url = $image->getAttribute('ref');
// 		$url = trim($url);
// 		$img_urls [$i] = $url;//'http://streeteasy.com' . $url;
// 		error_log("Image URL: ".$url);
// 		$resultSet->addPropertyImageLinkByIndex ( $i,  $url);
// 	}
	
// 	foreach ( $item->find('div.listing-faces div.listing-face div.listing-face-content div.content-container div.listing-column-photo div.listing-img a.listing-url') as $link ) {
// 		$url = $link->href;
// 		$url = trim($url);
// 		error_log("Link: ".$url);
// 		$resultSet->addPropertyDetailLinkByIndex ( $i, $url );
// 	}
	
// 	$i++;
// }

// $is_next_enabled = true;
// $is_prev_enabled = true;
// $pagination_text = '';

// $prev_link_obj = $html->find('div.pager ul li.prev', 0);
// if(empty($prev_link_obj)) {
// 	$is_prev_enabled = false;
// }
// else {
// 	$prev_link_class = $prev_link_obj->getAttribute('class');
// 	if(strpos($prev_link_class, ' disabled') !== false) 
// 		$is_prev_enabled = false;
// }

// $next_link_obj = $html->find('div.pager ul li.next', 0);
// if(empty($next_link_obj)) {
// 	$is_next_enabled = false;
// }
// else {
// 	$next_link_class = $next_link_obj->getAttribute('class');
// 	if(strpos($next_link_class, ' disabled') !== false)
// 		$is_next_enabled = false;
// 	$pagination_text = $html->find('div.pager ul li.page', 0)->plaintext;
// }
// save_search(array('keywords' => $keywords, 'startDate' => $startDate, 'endDate' => $endDate, 'sleeps' => $sleeps, 'sort' => $sort), $resultSet->results, $page);
?>