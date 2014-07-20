<?php

require_once 'CrawlerAbstract.php';
require_once 'Entity/Listing.php';

/**
 * Class CrawlerListingSearch
 *
 * @package HomeAway
 * @author Valdas Petrulis <petrulis.valdas@gmail.com>
 */
class CrawlerListingSearch
    extends CrawlerAbstract
{

    /**
     * Main project URL
     *
     * @var string
     */
    private $urlBase = 'http://www.homeaway.com';

    /**
     * URL, there all available flats can be found
     *
     * @var string
     */
    // private $urlSearch = 'http://www.homeaway.com/search/';
    // private $urlSearch = 'http://www.homeaway.com/search/keywords:Family/arrival:2014-04-24/departure:2014-04-25/minSleeps/3/page:2'
    private $urlSearch = 'http://www.homeaway.com/search/new-jersey/region:1026/keywords:New+jersey/arrival:2014-07-18/departure:2014-07-18/minPrice/5000';

    /**
     * Performs search request
     *
     * @return Listing[] List of listings IDs
     * @throws \UnexpectedValueException
     */
    public function requestSearch()
    {
        // Load HTML to DOM object
        $html = $this->request($this->urlSearch);
        $dom = new simple_html_dom();
        $dom->load($html);

        // How many flats found?
        $searchCount = $dom->find('.container-search-results .number-results-text', 0);
        if(!$searchCount) {
            throw new \UnexpectedValueException('Search results not found');
        }
        $this->log(sprintf(
            "Search results: %s",
            $searchCount->plaintext
        ));

        // Retrieve each flat
        $listings = array();
        foreach ($dom->find('.container-search-results .listing-row') as $domListing) {
            $listing = new Listing();
            $listing->setId(
                trim($domListing->find('.listing-face-content .listing-propertyid', 0)->plaintext, '# ')
            );
            $listing->setTitle(
                trim($domListing->find('.listing-face-content .listing-title', 0)->plaintext)
            );
            $listing->setUrlDetail(
                $this->urlBase . $domListing->find('.listing-face-content .listing-url', 0)->href
            );
            /** @var simple_html_dom_node $domListing */
            $this->log(sprintf(
                "\tID=%s, URL=%s, title=\"%s\"",
                $listing->getId(),
                $listing->getUrlDetail(),
                $listing->getTitle()
            ));

            $listings[] = $listing;
        }

        return $listings;
    }

    /**
     * Performs request of listing details
     *
     * @param string $id
     * @param Listing $listing
     *
     * @return Listing
     * @throws \UnexpectedValueException
     */
    public function requestDetails($id, $listing=null)
    {
        if(!isset($listing)) {
            $listing = new Listing();
        }
        $listing->setId($id);

        return $this->requestDetailsByUrl(
            $this->urlBase . '/vacation-rental/p' . $id,
            $listing
        );
    }

    /**
     * Performs request of listing details
     *
     * @param string $url
     * @param Listing $listing
     *
     * @return Listing
     * @throws \UnexpectedValueException
     */
    public function requestDetailsByUrl($url, $listing=null)
    {
        if(!isset($listing)) {
            $listing = new Listing();
        }

        // Load HTML to DOM object
        $html = $this->request($url);
        $dom = new simple_html_dom();
        $dom->load($html);

        // Load HTML to DOM object
        $dom = new simple_html_dom();
        $dom->load($html);

        return $listing;

        // How many flats found?
        $searchCount = $dom->find('.container-search-results .number-results-text', 0);
        if(!$searchCount) {
            throw new \UnexpectedValueException('Search results not found');
        }
        $this->log(sprintf(
                "Search results: %s",
                $searchCount->plaintext
            ));

        // Retrieve each flat
        $listings = array();
        foreach ($dom->find('.container-search-results .listing-row') as $listing) {
            $listings[] = $listingUrl = $this->urlBase . $listing->find('.listing-face-content .listing-url', 0)->href;
            /** @var simple_html_dom_node $listing */
            $this->log(sprintf(
                'URL: %s, title="%s"',
                $listingUrl,
                trim($listing->find('.listing-face-content .listing-title', 0)->plaintext)
            ));
        }

        return $listings;
    }
} 