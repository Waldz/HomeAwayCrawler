<?php

namespace FlatFindr\HomeAway;

use Doctrine\ORM\EntityManager;
use FlatFindr\Entity\Listing;

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
    private $urlSearch = 'http://www.homeaway.com/search/new-jersey/region:1026/keywords:New+jersey/arrival:2014-07-18/departure:2014-07-18/minPrice/5000';

    /**
     * @param EntityManager $entityManager
     */
    public function __construct($entityManager)
    {
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
     * @return Listing[] List of listings IDs
     * @throws \UnexpectedValueException
     */
    public function requestSearch()
    {
        $orm = $this->getEntityManager();

        // Load HTML to DOM object
        $html = $this->request($this->urlSearch);
        $dom = new \simple_html_dom();
        $dom->load($html);

        // How many flats found?
        $searchCount = $dom->find('.container-search-results .number-results-text', 0);
        if(!$searchCount) {
            throw new \UnexpectedValueException('Search results not found');
        }
        $this->log(sprintf(
            "Search results: %s",
            $searchCount->text()
        ));

        // Retrieve each flat
        $listings = array();
        foreach ($dom->find('.container-search-results .listing-row') as $domListing) {
            /** @var simple_html_dom_node $domListing */
            $listing = $this->factoryListing(
                trim($domListing->find('.listing-face-content .listing-propertyid', 0)->text(), '# ')
            );
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
        $domTitle = $dom->find('#wrapper .hidden-phone h1', 0);
        if(!$domTitle) {
            throw new \UnexpectedValueException('Flat details title not found');
        }

        // Flat description
        if( $domDescription=$dom->find('#wrapper .description-wrapper .property-description', 0) ) {
            $listing->setDescription(
                trim($domDescription->text())
            );
        }

        // Owner name
        if( $domOwner=$dom->find('#wrapper .contact-info-wrapper h3', 0) ) {
            $listing->setOwner(
                trim($domOwner->text())
            );
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
            $this->urlBase . '/vacation-rental/p' . $id
        );
    }
}