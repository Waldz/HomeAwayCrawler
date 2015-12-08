<?php

namespace FlatFindr\HomeAway;

use Doctrine\ORM\EntityManager;
use FlatFindr\Entity\Listing;
use FlatFindr\Entity\ListingAmenity;
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
     * URL, there all reagions can be found
     *
     * @var string
     */
    private $urlRegions = 'http://www.homeaway.com/vacation-rentals/world/r1';

    /**
     * URL, there all available flats can be found
     *
     * @var string
     */
    // private $urlSearch = 'http://www.homeaway.com/search/';
    // private $urlSearch = 'http://www.homeaway.com/vacation-rentals/world/r1';
    // private $urlSearch = 'http://www.homeaway.com/search/new-jersey/region:1026';
    // private $urlSearch = 'http://www.homeaway.com/vacation-rentals/new-york/r50';
    private $urlSearch = 'http://www.homeaway.com/vacation-rentals/new-york/new-york-city/r1737';
    //private $urlSearch = 'http://www.homeaway.com/search/new-jersey/region:1026/keywords:New+jersey/arrival:2014-07-18/departure:2014-07-18/minPrice/2000';

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
            $listing->setStatus(Listing::STATUS_NEW);
            $listing->setDateCreate(new \DateTime());
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
    public function requestRegionsAll()
    {
        $url = $this->urlRegions;

        $regions = array();
        while( !empty($url) ) {
            // Process one page
            /** @var \simple_html_dom $dom */
            $regions = array_merge(
                $regions,
                $this->requestRegions($url, $dom)
            );

            // If another page exists
            $domRegions = $dom->find('.gbs-tree ul.gbs-node-list li.');
            foreach($domRegions as $domRegion) {
                /** @var \simple_html_dom_node $domRegion */
            }
        }
    }

    /**
     * Process regions results page
     *
     * @param string $url WHere to search
     * @param \simple_html_dom $dom
     *
     * @return Listing[] List of listings
     * @throws \UnexpectedValueException
     */
    private function requestRegions($url, &$dom)
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
                trim($domListing->find('.listing-face-content .property-id', 0)->text(), '# ')
            );
            /** @noinspection PhpUndefinedMethodInspection */
            $listing->setTitle(
                trim($domListing->find('.listing-face-content .listing-title', 0)->text())
            );
            $listing->setUrlDetail(
                $this->urlBase . $domListing->find('.listing-face-content .listing-url', 0)->href
            );

            // Update DB
            $listing->setDateNextSync(new \DateTime());
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
     * Process search results page
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
                trim($domListing->find('.listing-face-content .property-id', 0)->text(), '# ')
            );
            /** @noinspection PhpUndefinedMethodInspection */
            $listing->setTitle(
                trim($domListing->find('.listing-face-content .listing-title', 0)->text())
            );
            $listing->setUrlDetail(
                $this->urlBase . $domListing->find('.listing-face-content .listing-url', 0)->href
            );

            // Update DB
            $listing->setDateNextSync(new \DateTime());
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
    public function requestDetailsByUrl(Listing $listing, $url)
    {
        $orm = $this->getEntityManager();

        $listing->setDateSync(new \DateTime());
        try {
            // Load HTML to DOM object
            $html = $this->request($url);
            $dom = new \simple_html_dom();
            $dom->load($html);
        } catch(\Exception $e) {
            $this->log(sprintf(
                '[ERROR] %s: %s',
                get_class($e),
                $e->getMessage()
            ));

            // Listing has gone
            $listing->setStatus(Listing::STATUS_ARCHIVED);
            $listing->setDateArchive(new \DateTime());
            $listing->setDateNextSyncAfterFail();
            $orm->persist($listing);
            $orm->flush();

            return $listing;
        }

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

        // JSON analytics structure
        if( preg_match('/var analyticsdatalayer = ({.+});/', $html, $matches) ) {
            $jsonAnalytics = json_decode($matches[1], true);
        } else {
            $jsonAnalytics = null;
        }

        // JSON structure
        if( preg_match('/var unitJSON = ({.+});/', $html, $matches) ) {
            $listing->setJsonData($matches[1]);
            $json = json_decode($matches[1], true);
        } else {
            throw new \UnexpectedValueException('JSON details not found');
        }

        // Prices
        $this->parsePrices($listing, $dom, $jsonAnalytics);
        // Amenities
        $this->parseAmenities($listing, $dom);
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

        // Update DB
        $listing->setStatus(Listing::STATUS_ACTIVE);
        $listing->setDateUpdate(new \DateTime());
        $listing->setDateNextSync((new \DateTime())->add(new \DateInterval('P1D')));
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
    public function requestDetails(Listing $listing)
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
    protected function parseJsonPhones(Listing $listing, $json)
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
    protected function parseJsonPhotos(Listing $listing, $json)
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
    protected function parseJsonLocations(Listing $listing, $json)
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
     * @param array $jsonAnalytics
     *
     * @throws \UnexpectedValueException
     */
    protected function parsePrices(Listing $listing, $dom, $jsonAnalytics)
    {
        /** @var \simple_html_dom_node $domTable */
        if(!$dom->find('#rentalRates', 0)) {
            throw new \UnexpectedValueException('Price details not found');
        }

        $domTable = $dom->find('table.ratesTable', 0);
        if(!$domTable) {
            return;
        }

        $basis = trim($dom->find('#rentalRates .basis dd', 0)->text());
        if($basis=='Per property') {
            $basis = ListingPrice::BASIS_OVERAL;
        } elseif($basis=='Per person') {
            $basis = ListingPrice::BASIS_PERSON;
        } else {
            throw new \UnexpectedValueException('Price basis unknown');
        }

        //$currency = trim($dom->find('#rentalRates .units dd', 0)->text());
        $currency = strtoupper($jsonAnalytics['currency']);
        if(empty($currency)) {
            throw new \UnexpectedValueException('Price currency not found');
        }

        $i = 1;
        foreach($domTable->find('table.ratesTable tr.ratePeriodLabel') as $domRow) {
            /** @var \simple_html_dom_node $domRow */
            $dateText = trim($domRow->find('td.alt .ratePeriodDates', 0)->text());
            $dateTitle = trim($domRow->find('td.alt .ratePeriodTitle', 0)->text());
            if(empty($dateText)) {
                $this->log(sprintf(
                    '[NOTICE] Found price with title "%s", but wrong date: "%s"',
                    $dateTitle,
                    $dateText
                ));
                continue;
            }

            // Format "Nov 27 2014 - Nov 27 2014"
            if(preg_match('/(.* \d* \d*)\s+-\s+(.* \d* \d*)/', $dateText, $matches)) {
                $dateStart = new \DateTime($matches[1]);
                $dateEnd = new \DateTime($matches[2]);
            // Format "Nov 27 - Nov 27"
            } elseif(preg_match('/(.* \d*)\s+-\s+(.* \d*)/', $dateText, $matches)) {
                $dateStart = new \DateTime($matches[1]);
                $dateEnd = new \DateTime($matches[2]);
            } else {
                throw new \UnexpectedValueException('Price date not found');
            }

            $this->parsePriceByType(
                $listing, ListingPrice::TYPE_DAILY, $basis, $dateStart, $dateEnd, $currency,
                $domRow->find('td.nightly .rate', 0),
                $domRow->find('td.nightly .ratenote', 0)
            );
            $this->parsePriceByType(
                $listing, ListingPrice::TYPE_WEEKEND, $basis, $dateStart, $dateEnd, $currency,
                $domRow->find('td.weekendNight .rate', 0),
                $domRow->find('td.weekendNight .ratenote', 0)
            );
            $this->parsePriceByType(
                $listing, ListingPrice::TYPE_WEEKLY, $basis, $dateStart, $dateEnd, $currency,
                $domRow->find('td.weekly .rate', 0),
                $domRow->find('td.weekly .ratenote', 0)
            );
            $this->parsePriceByType(
                $listing, ListingPrice::TYPE_MONTHLY, $basis, $dateStart, $dateEnd, $currency,
                $domRow->find('td.monthly .rate', 0),
                $domRow->find('td.monthly .ratenote', 0)
            );
            $this->parsePriceByType(
                $listing, ListingPrice::TYPE_EVENT, $basis, $dateStart, $dateEnd, $currency,
                $domRow->find('td.event .rate', 0),
                $domRow->find('td.event .ratenote', 0)
            );

            $i++;
        }
    }

    /**
     * @param Listing $listing
     * @param string $type
     * @param string $basis
     * @param \DateTime $dateStart
     * @param \DateTime $dateEnd
     * @param string $currency
     * @param \simple_html_dom_node|array $priceDom
     * @param \simple_html_dom_node|array $priceNotes
     *
     * @throws \UnexpectedValueException
     */
    protected function parsePriceByType(
        Listing $listing,
        $type, $basis, \DateTime $dateStart, \DateTime $dateEnd, $currency,
        $priceDom, $priceNotes
    )
    {
        if(!isset($priceDom)) {
            return;
        };
        $price = '';
        foreach($priceDom->nodes as $node) {
            if($node->tag=='text') {
                $price .= trim($node->text());
            }
        }
        if(empty($price)) {
            return;
        }
        $price = str_replace(array('$', ','), '', trim($price));;
        if(!is_numeric($price)) {
            throw new \UnexpectedValueException(sprintf(
                'Price ammount not found in "%s"',
                trim($priceDom->text())
            ));
        }
        $priceNotes = trim($priceNotes->text());

        $listingPrice = $listing->getPrice($type, $dateStart, $dateEnd);
        if(!isset($listingPrice)) {
            $listingPrice = new ListingPrice();
            $listing->addPrice($listingPrice);
        }
        $listingPrice
            ->setType($type)
            ->setBasis($basis)
            ->setDateStart($dateStart)
            ->setDateEnd($dateEnd)
            ->setPrice($price)
            ->setCurrency($currency)
            ->setNotes($priceNotes);
    }

    /**
     * @param Listing $listing
     * @param \simple_html_dom $dom
     *
     * @throws \UnexpectedValueException
     */
    protected function parseAmenities(Listing $listing, $dom)
    {
        /** @var \simple_html_dom_node $domTable */
        $domTable = $dom->find('#amenities-container', 0);
        if(!$domTable) {
            throw new \UnexpectedValueException('Amenities details not found');
        }

        // Delete old amenities
        $query = $this->getEntityManager()->createQueryBuilder()
            ->delete('FlatFindr\Entity\ListingAmenity', 'A')
            ->where('A.listing = :listing')
            ->setParameter('listing', $listing->getId());
        $query->getQuery()->execute();

        $i = 1;
        foreach($domTable->find('.row-fluid') as $domRow) {
            /** @var \simple_html_dom_node $domRow */
            /** @var \simple_html_dom_node $domName */
            $domName = $domRow->find('div', 0);
            if($domName->hasAttribute('id')) {
                switch($domName->getAttribute('id')) {
                    case 'propertyType':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_PROPERTY_TYPE, $domRow);
                        break;

                    case 'buildingtype':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_BUILDING_TYPE, $domRow);
                        break;

                    case 'formsofpayment':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_PAYMENT_TYPE, $domRow);
                        break;

                    case 'accommodationType':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_ACCOMMODATION_TYPE, $domRow);
                        break;

                    case 'locationType':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_LOCATION_TYPE, $domRow);
                        break;

                    case 'theme':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_THEME, $domRow);
                        break;

                    case 'general':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_GENERAL, $domRow);
                        break;

                    case 'kitchen':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_KITCHEN, $domRow);
                        break;

                    case 'dining':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_DINING, $domRow);
                        break;

                    case 'entertainment':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_ENTERTAINMENT, $domRow);
                        break;

                    case 'communicationInternet':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_COMMUNICATIONS, $domRow);
                        break;

                    case 'outside':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_OUTSIDE, $domRow);
                        break;

                    case 'suitability':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_SUITABILITY, $domRow);
                        break;

                    case 'poolSpa':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_POOL, $domRow);
                        break;

                    case 'onsiteServices':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_SERVICES_ONSITE, $domRow);
                        break;

                    case 'attractions':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_ATTRACTIONS, $domRow);
                        break;

                    case 'leisureActivities':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_LEISURE, $domRow);
                        break;

                    case 'localservicesandbusinesses':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_SERVICES, $domRow);
                        break;

                    case 'sportsandadventureactivities':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_SPORTS, $domRow);
                        break;

                    default:
                        throw new \UnexpectedValueException(sprintf(
                            'Amenity id not recognized: %s',
                            $domName->getAttribute('id')
                        ));
                        break;
                }
            } else {
                switch($domName->text()) {
                    case 'Meals:':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_MEALS, $domRow);
                        break;

                    case 'Floor Area:':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_AREA, $domRow);
                        break;

                    case 'Bedrooms:':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_BEDROOM, $domRow);
                        break;

                    case 'Bathrooms:':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_BATHROOM, $domRow);
                        break;

                    case 'Notes:':
                        $this->parseAmenityByType($listing, ListingAmenity::NAME_BATHROOM, $domRow);
                        break;

                    default:
                        throw new \UnexpectedValueException(sprintf(
                            'Amenity name not recognized: %s',
                            $domName->text()
                        ));
                        break;
                }
            }

            $i++;
        }
    }

    /**
     * @param Listing $listing
     * @param string $name
     * @param \simple_html_dom_node $domRow
     */
    protected function parseAmenityByType(Listing $listing, $name, $domRow)
    {
        foreach($domRow->find('div ul li') as $domValue) {
            /** @var \simple_html_dom_node $domValue */
            $value = trim($domValue->text());
            if(!empty($value)) {
                $amenity = new ListingAmenity();
                $amenity->setName($name)->setValue($value);
                $listing->addAmenity($amenity);
            }
        }
    }
}