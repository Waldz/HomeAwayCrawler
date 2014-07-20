<?php

require_once 'library/simple_html_dom.php';
require_once 'includes/record_searches.php';
require_once 'includes/HomeAway/CrawlerListingSearch.php';

defined('APP_ENVIRONMENT') or define('APP_ENVIRONMENT', 'development');
//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL);

$crawler = new CrawlerListingSearch();
if(APP_ENVIRONMENT=='development') {
    $crawler->setCacheEnabled(true)
        ->setCacheDirectory(dirname(__FILE__).'/data/cache/');
}

// Go thru all flats in da search
$listingUrls = $crawler->requestSearch();
foreach($listingUrls as $listing) {
    var_dump($listing);
    $listing = $crawler->requestDetailsByUrl($listing->getUrlDetail(), $listing);
}

