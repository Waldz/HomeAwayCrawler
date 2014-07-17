<?php
include('Dom/ResultSet.php');
include('Dom/Scrape.php');
include('Dom/Result.php');		

$feed_url = "http://streeteasy.com/nyc/rentals/nyc/";	

if (isset($_POST['do_search']) || 1==1 || $_GET['submit'] == 1)
{
    if($_GET['submit'] == 1){
      $rental = $_GET['rental_type'];
      $priceFrom = $_GET['price_from'];
      $priceTo = $_GET['price_to'];
      $beds = $_GET['beds'];
      $baths = $_GET['baths'];
      $page = $_GET['page'];
      $sort = $_GET['sort_by'];
    } else {
      $rental = $_POST['rental_type'];
      $priceFrom = $_POST['price_from'];
      $priceTo = $_POST['price_to'];
      $beds = $_POST['beds'];
      $baths = $_POST['baths'];
      $page = $_POST['page'];
      $sort = $_POST['sort'];
    }
    
    $rentType = "";
    
    if($priceFrom == '$ MIN') $priceFrom = '';
    if($priceTo == '$ MAX') $priceTo = '';

		for($i=0; $i<count($rental); $i++) {

			$rentType .= $rental[$i]; 

			if ($i < count($rental)-1)

			$rentType .= ',';

		}

		if ($rental != "") {

			$feed_url = $feed_url."rental_type:{$rentType}|";

		}

		if ($priceFrom != "" && $priceTo != "") {

			$feed_url = $feed_url."price:".$priceFrom."-".$priceTo.'|';

		} else if($priceFrom != "") {

			$feed_url = $feed_url."price:".$priceFrom."-".'|';

		} else if($priceTo != "") {

			$feed_url = $feed_url."price:"."-".$priceTo.'|';

		}

		if ($beds != "") {

			$feed_url = $feed_url."beds:".$beds."|";

		}

		if ($baths != "") {

			$feed_url = $feed_url."baths".$baths;

		}

		

		

	} 
    if (!isset($sort) || $sort == "" || ($sort != "price_desc" && $sort != "price_asc")) {
      
      $sort = 'price_asc';
		} 
      
    $feed_url.='?sort_by='.$sort;
    

		

		if (!isset($page) && $page == "") {
      $page = '1';
		}
    $feed_url = $feed_url.'&page='.$page;

		



//$markup = file_get_contents($feed_url);

		/*$ch = curl_init();

		$timeout = 5;

		curl_setopt($ch,CURLOPT_URL,$feed_url);

		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);

		$markup = curl_exec($ch);

		curl_close($ch);

		echo "<div id='showTempData' style='display:none'>";

		//print_r($markup);

		echo "</div>";
*/
		

		



	$resultSet = new Property_Dom_ResultSet();

       
        
	$markup = file_get_contents($feed_url);
        
	$dom = new Zend_Dom_Query($markup);

	//echo "<pre>";//print_r($dom);exit;
	$headings = $dom -> query("div.body h3");

	$descriptions = $dom -> query("div.item_inner div.body");

	$descriptions2 = $dom -> query("div.item_inner div.more");

	$links =  $dom -> query("div.item_inner div.photo img");
	

	$pager = $dom->query("div.pager_bottom a");

	

	$detail_urls = $dom->query("div.body h3 a");
  
	

	$prev_next_count=count($pager); //if 2 prev/next link.. if 1 just next link

	

	$loopcnt=0;

	$max_page = preg_match("/(?U)class=\'pagination\_jump\'\>\sof\s(.+)\s/is", $markup, $m) ? str_replace(',', '', trim($m[1])) : 1;
  if($max_page > 100) $max_page = 100;
  
	/*foreach($pager as $page)

	{
		$loopcnt++;

		

		if($prev_next_count==1)	

			$next=$page->getAttribute('href');

		else

		{

			if($loopcnt==1)

				$prev=$page->getAttribute('href');

			else

				$next=$page->getAttribute('href');	

		}	

		

//		echo $page->getAttribute('href')."<HR>";

//		echo $page->textContent;

	}*/

	



	

	$i = 0;

	foreach($headings as $heading) {

		

		$result = new Property_Dom_Result();

		$result -> type = 'PAID';

		$result -> position = $i + 1;

		$resultSet -> addResult($result);

		$resultSet -> addTitleByIndex($i, $heading -> textContent);



		

		$i++;

	}

	$i=0;

	

	foreach ($descriptions as $description) {

		

		$resultSet -> addDescriptionByIndex($i, $description -> textContent);

		$i++;

	}

	

	$i = 0;

	foreach ($descriptions2 as $description2) {

		

		$resultSet -> addDescriptionMoreByIndex($i, $description2 -> textContent);

		$i++;

	}

	

	$i = 0;
  $img_urls = array();
	foreach ($detail_urls as $link) {
    $url = $link -> getAttribute('href');
    $featured[$i] = 0;
    if(strpos($url, '?featured=1')!== FALSE){
      $featured[$i] = 1;
      $url = str_replace('?featured=1', '', $url);
    }
    $img_urls[$i] = 'http://streeteasy.com'.$url;
		$resultSet -> addPropertyDetailLinkByIndex($i, $url);

		$i++;

	}

  foreach($featured as $key => $f){
    $resultSet -> addPropertyFeaturedByIndex($key, $f);
  }

	foreach ($img_urls as $key => $imglink) {

		$ch = curl_init();

		curl_setopt($ch,CURLOPT_URL, $imglink);

		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);

		$page_img = curl_exec($ch);

		curl_close($ch);
    
    $img = (preg_match('/(?U)id="large_photo"(.*)src="(.+)"/is', $page_img, $m)) ? $m[2] : 'http://streeteasy.com/images/misc/no_photo_160.jpg';
    $resultSet -> addPropertyImageLinkByIndex($key, $img);


	}	

//$links->current()->getAttribute('src');	

?>