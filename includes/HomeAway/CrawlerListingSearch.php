<?php

namespace FlatFindr\HomeAway;

use Doctrine\ORM\EntityManager;
use FlatFindr\Entity\Listing;
use FlatFindr\Entity\ListingLocation;
use FlatFindr\Entity\ListingPhone;
use FlatFindr\Entity\ListingPhoto;
use FlatFindr\Entity\ListingPrice;

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
     * @vat EntityManager
     */
    private $entityManager;

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
    // private $urlSearch = 'http://www.homeaway.com/search/new-jersey/region:1026';
    // private $urlSearch = 'http://www.homeaway.com/vacation-rentals/new-york/r50';
    private $urlSearch = 'http://www.homeaway.com/search/new-jersey/region:1026/keywords:New+jersey/arrival:2014-07-18/departure:2014-07-18/minPrice/5000';

    /**
     * @param EntityManager $entityManager
     */
    public function __construct($entityManager)
    {
        parent::__construct();
        $this->setEntityManager($entityManager);
    }

    /**
     * Sets entityManager.
     *
     * @param EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Retrieves entityManager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param string $ident
     * @return Listing
     */
    public function factoryListing($ident)
    {
        $repository = $this->getEntityManager()->getRepository('FlatFindr\Entity\Listing');

        /** @var Listing $listing */
        $listing = $repository->findOneBy(array(
            'provider' => Listing::PROVIDER_HOMEAWAY,
            'providerIdent' => $ident,
        ));
        if(!$listing) {
            $listing = new Listing();
            $listing->setProvider(Listing::PROVIDER_HOMEAWAY);
            $listing->setProviderIdent($ident);
        }

        return $listing;
    }

    /**
     * Performs search request
     *
     * @return Listing[] List of listings
     * @throws \UnexpectedValueException
     */
    public function requestSearchAll()
    {
        $url = $this->urlSearch;

        $listings = array();
        while( !empty($url) ) {
            // Process one page
            /** @var \simple_html_dom $dom */
            $listings = array_merge(
                $listings,
                $this->requestSearch($url, $dom)
            );

            // If another page exists
            /** @var \simple_html_dom_node $domNext */
            $domNext = $dom->find('.search-results-column .next', 0);
            if( $domNext && strpos($domNext->getAttribute('class'), 'disabled')==0 ) {
                $url = $this->urlBase . $domNext->find('a', 0)->href;
            // It was da last page
            } else {
                break;
            }
        }

        return $listings;
    }

    /**
     * Proccess search results page
     *
     * @param string $url WHere to search
     * @param \simple_html_dom $dom
     *
     * @return Listing[] List of listings
     * @throws \UnexpectedValueException
     */
    private function requestSearch($url, &$dom)
    {
        $orm = $this->getEntityManager();

        // Load HTML to DOM object
        $html = $this->request($url);
        $dom = new \simple_html_dom();
        $dom->load($html);

        // How many flats found?
        /** @var \simple_html_dom_node $searchCount */
        $searchCount = $dom->find('.container-search-results .number-results-text', 0);
        /** @var \simple_html_dom_node $searchPage */
        $searchPage = $dom->find('.search-results-column .page', 0);
        if(!$searchCount) {
            throw new \UnexpectedValueException('Search results not found');
        }
        $this->log(sprintf(
            "Search results: %s (%s)",
            $searchCount->text(),
            $searchPage ? $searchPage->text() : ''
        ));

        // Retrieve each flat
        $listings = array();
        foreach ($dom->find('.container-search-results .listing-row') as $domListing) {
            /** @var \simple_html_dom_node $domListing */
            /** @noinspection PhpUndefinedMethodInspection */
            $listing = $this->factoryListing(
                trim($domListing->find('.listing-face-content .listing-propertyid', 0)->text(), '# ')
            );
            /** @noinspection PhpUndefinedMethodInspection */
            $listing->setTitle(
                trim($domListing->find('.listing-face-content .listing-title', 0)->text())
            );
            $listing->setUrlDetail(
                $this->urlBase . $domListing->find('.listing-face-content .listing-url', 0)->href
            );

            // Update DB
            $orm->persist($listing);
            $orm->flush();

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
     * @param Listing $listing
     * @param string $url
     *
     * @return Listing
     * @throws \UnexpectedValueException
     */
    public function requestDetailsByUrl($listing, $url)
    {
        $orm = $this->getEntityManager();

        // Load HTML to DOM object
        $html = $this->request($url);
        $dom = new \simple_html_dom();
        $dom->load($html);

        // Flat title
        /** @var \simple_html_dom_node $domTitle */
        $domTitle = $dom->find('#wrapper .hidden-phone h1', 0);
        if(!$domTitle) {
            throw new \UnexpectedValueException('Flat details title not found');
        }

        // Flat description
        /** @var \simple_html_dom_node $domDescription */
        if( $domDescription=$dom->find('#wrapper .description-wrapper .property-description', 0) ) {
            $listing->setDescription(
                trim($domDescription->text())
            );
        }

        // Owner name
        /** @var \simple_html_dom_node $domOwner */
        if( $domOwner=$dom->find('#wrapper .contact-info-wrapper h3', 0) ) {
            $listing->setOwner(
                trim($domOwner->text())
            );
        }

        // Prices
        $this->parseJsonPrices($listing, $dom);

        // JSON structure
        preg_match('/var unitJSON = ({.*});/', $html, $matches) && !empty($matches[1]);
        if(count($matches)>0 && !empty($matches[1])) {
            $listing->setJsonData($matches[1]);
            $json = json_decode($matches[1], true);

            // Phones
            $this->parseJsonPhones($listing, $json);
            // Locations
            $this->parseJsonLocations($listing, $json);
            // Photos
            $this->parseJsonPhotos($listing, $json);

            // Other info
            $listing->setUrlSphere($json['property']['sphereUrl']);
            if(!empty($json['property']['videoUrls'])) {
                $this->log(sprintf(
                    '[NOTICE] Found %d videos',
                    count($json['property']['videoUrls'])
                ));
            }

            //var_export( array_keys($json) );
            unset(
                $json['images'],
                $json['property']['imageUrls'],
                $json['property']['contact'],
                $json['contact']['phones'],
                $json['contact']['phonesByTitle'],
                $json['contact']['primaryPhone'],
                $json['availabilityCalendar']
            );
            //var_export($json);
        } else {
            throw new \UnexpectedValueException('JSON details not found');
        }

        // Update DB
        $orm->persist($listing);
        $orm->flush();

        return $listing;
    }

    /**
     * Performs request of listing details
     *
     * @param Listing $listing
     *
     * @return Listing
     * @throws \UnexpectedValueException
     */
    public function requestDetails($listing)
    {
        return $this->requestDetailsByUrl(
            $listing,
            $this->urlBase . '/vacation-rental/p' . $listing->getProviderIdent()
        );
    }

    /**
     * @param Listing $listing
     * @param array $json JSON structure about listing from HomeAway
     */
    protected function parseJsonPhones($listing, $json)
    {
//        $json['contact']['contactId'];
//        $json['contact']['contactId'];
//        $json['contact']['languagesSpoken'];
//        $json['contact']['otherLanguages'];
//        $json['contact']['hasEmail'];

        $i = 1;
        foreach($json['contact']['phones'] as $jsonPhone) {
            $listingPhone = $listing->getPhone($jsonPhone['phoneNumber']);
            if(!isset($listingPhone)) {
                $listingPhone = new ListingPhone();
                $listing->addPhone($listingPhone);
            }
            $listingPhone
                ->setCountryCode($jsonPhone['countryCode'])
                ->setExtensionCode($jsonPhone['extension'])
                ->setPhone($jsonPhone['phoneNumber'])
                ->setNotes($jsonPhone['notes']);

            if($i==1) {
                $listingPhone->setType(ListingPhone::TYPE_PRIMARY);
            } elseif($i==2) {
                $listingPhone->setType(ListingPhone::TYPE_SECONDARY);
            } else {
                $listingPhone->setType(null);
            }

            $i++;
        }
    }

    /**
     * @param Listing $listing
     * @param array $json JSON structure about listing from HomeAway
     */
    protected function parseJsonPhotos($listing, $json)
    {
        $i = 1;
        foreach($json['images'] as $jsonPhoto) {
            $jsonImage = $jsonPhoto['imageFilesBySize']['1024x768'];

            $listingPhoto = $listing->getPhoto($jsonImage['uri']);
            if(!isset($listingPhoto)) {
                $listingPhoto = new ListingPhoto();
                $listing->addPhoto($listingPhoto);
            }
            $listingPhoto
                ->setOrder($jsonPhoto['displayOrder'])
                ->setType($jsonPhoto['type'])
                ->setNotes($jsonPhoto['note'])
                ->setUrl($jsonImage['uri'])
                ->setUrlSecure($jsonImage['secureUri'])
                ->setSize($jsonImage['imageSize'])
                ->setWidth($jsonImage['width'])
                ->setHeight($jsonImage['height']);
            $i++;
        }
    }

    /**
     * @param Listing $listing
     * @param array $json JSON structure about listing from HomeAway
     *
     * @throws \UnexpectedValueException
     */
    protected function parseJsonLocations($listing, $json)
    {
        if(empty($json['property']['googleLocationJson'])) {
            throw new \UnexpectedValueException('Location JSON not found');
        }

        $jsonLocations = json_decode($json['property']['googleLocationJson'], true);
        $i = 1;
        foreach($jsonLocations['location'] as $jsonLocation) {
            $latitude = urldecode($jsonLocation['a']);
            $longitude = urldecode($jsonLocation['b']);

            $listingLocation = $listing->getLocation($latitude, $longitude);
            if(!isset($listingLocation)) {
                $listingLocation = new ListingLocation();
                $listing->addLocation($listingLocation);
            }
            $listingLocation
                ->setLatitude($latitude)
                ->setLongitude($longitude)
                ->setZoom($jsonLocation['zoom'])
                ->setZoomMax($jsonLocation['maxZoom'])
                ->setExact($jsonLocation['exact'])
                ->setIsValid($jsonLocation['addressLatLngIsValid']);

            if($jsonLocation['type']!='null') {
                $listingLocation->setType($jsonLocation['type']);
            } else {
                $listingLocation->setType(null);
            }
            $i++;
        }
    }

    /**
     * @param Listing $listing
     * @param \simple_html_dom $dom
     *
     * @throws \UnexpectedValueException
     */
    protected function parseJsonPrices($listing, $dom)
    {
        /** @var \simple_html_dom_node $domTable */
        $domTable = $dom->find('table.ratesTable', 0);
        if(!$domTable) {
            throw new \UnexpectedValueException('Price details not found');
        }

        $i = 1;
        foreach($domTable->find('tr.ratePeriodLabel') as $domRow) {
            /** @var \simple_html_dom_node $domRow */
            /** @var \simple_html_dom_node $domDate */
            $domDate = $domRow->find('td.alt .ratePeriodDates ', 0);
            if(preg_match('/(.* \d* \d*) - (.* \d* \d*)/', trim($domDate->text()), $matches)) {
                $dateStart = new \DateTime($matches[1]);
                $dateEnd = new \DateTime($matches[2]);
            } else {
                throw new \UnexpectedValueException('Price date not found');
            }

            $price = trim($domRow->find('td.weekly .rate', 0)->text());
            $price = str_replace(array('$', ','), '', $price);
            $priceNotes = trim($domRow->find('td.weekly .ratenote', 0)->text());

            if(!empty($price)) {
                $listingPrice = $listing->getPrice(ListingPrice::TYPE_DAILY_WEEKLY, $dateStart, $dateEnd);
                if(!isset($listingPrice)) {
                    $listingPrice = new ListingPrice();
                    $listing->addPrice($listingPrice);
                }
                $listingPrice
                    ->setType(ListingPrice::TYPE_DAILY_WEEKLY)
                    ->setDateStart($dateStart)
                    ->setDateEnd($dateEnd)
                    ->setPrice($price)
                    ->setCurrency('USD')
                    ->setNotes($priceNotes);
            }

            $i++;
        }

    }
}