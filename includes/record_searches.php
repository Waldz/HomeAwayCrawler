<?php 

require_once 'connection.php';

function get_pending_cities() {
	
	$conn = db_connect();
	
	$sql = "SELECT * FROM cities_bk where status='0' order by city asc";
	error_log("Query: ".$sql);
	$result = mysql_query($sql);
	
	$cities = array();
	while ($row = mysql_fetch_assoc($result)) {
		$city = array();
		$city['city'] = $row['city'];
		//$city['state_code'] = $row['state_code'];
		$city['page'] = $row['page'];
		
		$cities[] = $city;
	}
	
	mysql_free_result($result);
	
	return $cities;
}

function get_pending_apartments() {

	$conn = db_connect();

	$keyword = '%';
	
	$sql = "SELECT * FROM apartments where status=0 and (title like '$keyword' OR description2 like '$keyword') order by detail_url asc limit 0,10000";
	error_log("Query: ".$sql);
	$result = mysql_query($sql);

	$apartments = array();
	while ($row = mysql_fetch_assoc($result)) {
		$apartment = array();
		$apartment['detail_url'] = $row['detail_url'];

		$apartments[] = $apartment;
	}

	mysql_free_result($result);

	return $apartments;
}

function search_apartments($keyword, $sleeps, $page) {
	
	error_log("page: $page");

	$conn = db_connect();
	
	$resultSet = new Property_Dom_ResultSet ();
	
	if(!empty($keyword))
		$keyword = '%'.$keyword.'%';
	$limit_count = 30;
	$limit_start = ($page-1) * $limit_count;

	if(empty($keyword))
		$sql = "SELECT * FROM apartments where description like '%Sleeps $sleeps%' order by id asc limit $limit_start, $limit_count";
	else $sql = "SELECT * FROM apartments where (title like '$keyword' OR description2 like '$keyword') AND description like '%Sleeps $sleeps%' order by id asc limit $limit_start, $limit_count";
	//echo $sql;die;
	error_log("Query: ".$sql);
	$result = mysql_query($sql);

	$i = 0;
	while ($row = mysql_fetch_assoc($result)) {
		
		$resultSet->addTitleByIndex ( $i, html_entity_decode($row['title']) );
		$resultSet->addDescriptionByIndex ( $i, html_entity_decode($row['description']) );
		$resultSet->addDescriptionByIndex ( $i, $row['description2'] );
		$resultSet->addPropertyImageLinkByIndex ( $i,  $row['thumb_url']);
		$resultSet->addPropertyDetailLinkByIndex ( $i, $row['detail_url'] );
		
		$i++;
	}

	mysql_free_result($result);

	return $resultSet;
}

function get_apartment_by_detail_url($detail_url) {

	$conn = db_connect();

	$sql = "SELECT * FROM apartments where detail_url='$detail_url' and status=1";

	error_log("Query: ".$sql);
	$result = mysql_query($sql);

	$apartment = array();
	if ($row = mysql_fetch_assoc($result)) {
		$apartment['markup'] = base64_decode($row['markup']);
		//error_log($row['markup']);
		$apartment['markup'] = gzinflate($apartment['markup']);
		$apartment['phone'] = json_decode($row['phone'], true);
		$apartment['location'] = json_decode($row['location'], true);

	}

	mysql_free_result($result);

	return $apartment;
}

function update_apartment($detail_url, $markup, $phone_numbers, $locations) {

	$conn = db_connect();
	$is_exists = false;

	$title = addslashes($markup);
	$phone_numbers = addslashes($phone_numbers);
	$locations = addslashes($locations);
	$markup = gzdeflate($markup);
	//error_log("compress: ".$markup);
	$markup = base64_encode($markup);
	//error_log("encode: ".$markup);

	//error_log("Length: ".strlen($markup));

	$sql = "UPDATE apartments set markup='$markup', phone='$phone_numbers', location='$locations', status=1 WHERE detail_url='$detail_url'";
	//error_log("Query: ".$sql);
	mysql_query($sql);
}

function update_city_page($city, $page) {

	$conn = db_connect();

	$sql = "UPDATE cities_bk set page=$page WHERE city='$city'";
	//error_log("Query: ".$sql);
	mysql_query($sql);
}

function update_city_status($city, $status) {

	$conn = db_connect();

	$sql = "UPDATE cities_bk set status='$status' WHERE city='$city'";
	//error_log("Query: ".$sql);
	mysql_query($sql);
}

function save_detail($url, $detail) {
	
	$conn = db_connect();
	
	$url = addslashes($url);
	$detail = addslashes(json_encode($detail));
	
	$sql = "INSERT INTO search_details(targeturl, details) VALUES('$url', '$detail')";
	//error_log("Query: ".$sql);
	mysql_query($sql);
}

function save_apartment($title, $description, $description2, $thumb_url, $detail_url, $page) {

	$conn = db_connect();
	$is_exists = false;
	
	$sql = "SELECT COUNT(*) as cnt FROM apartments where detail_url = '$detail_url'";
	$result = mysql_query($sql);
	if ($row = mysql_fetch_assoc($result)) {
		if($row['cnt'] > 0)
			$is_exists = true;
	}
	
	mysql_free_result($result);
	
	if($is_exists) 
		return;

	$title = addslashes($title);
	$description = addslashes($description);
	$description2 = addslashes($description2);
	$thumb_url = addslashes($thumb_url);
	$detail_url = addslashes($detail_url);

	$sql = "INSERT INTO apartments(title, description, description2, thumb_url, detail_url, page) VALUES('$title', '$description', '$description2', '$thumb_url', '$detail_url', $page)";
	//error_log("Query: ".$sql);
	mysql_query($sql);
}

?>