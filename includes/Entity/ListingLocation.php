<?php

namespace FlatFindr\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="listing_location"
 * )
 */
class ListingLocation
{

    /**
     * @var Listing
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="FlatFindr\Entity\Listing", inversedBy="locationList")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $listing;

    /**
     * @var double
     *
     * @ORM\Id
     * @ORM\Column(name="latitude", type="decimal", scale=12, precision=15, nullable=false)
     */
    private $latitude;

    /**
     * @var double
     *
     * @ORM\Id
     * @ORM\Column(name="longitude", type="decimal", scale=12, precision=15, nullable=false)
     */
    private $longitude;

    /**
     * @var int
     *
     * @ORM\Column(name="zoom", type="integer", length=3, nullable=true)
     */
    private $zoom;

    /**
     * @var int
     *
     * @ORM\Column(name="zoom_max", type="integer", length=3, nullable=true)
     */
    private $zoomMax;

    /**
     * @var boolean
     *
     * @ORM\Column(name="exact", type="boolean", nullable=true)
     */
    private $exact;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_valid", type="boolean", nullable=true)
     */
    private $isValid;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10, nullable=true)
     */
    private $type;

    /**
     * Sets listing.
     *
     * @param \FlatFindr\Entity\Listing $listing
     * @return $this
     */
    public function setListing($listing)
    {
        $this->listing = $listing;
        return $this;
    }

    /**
     * Retrieves listing.
     *
     * @return \FlatFindr\Entity\Listing
     */
    public function getListing()
    {
        return $this->listing;
    }

    /**
     * Sets latitude.
     *
     * @param double $latitude
     * @return $this
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * Retrieves latitude.
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Sets longitude.
     *
     * @param double $longitude
     * @return $this
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * Retrieves longitude.
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Sets zoom.
     *
     * @param int $zoom
     * @return $this
     */
    public function setZoom($zoom)
    {
        $this->zoom = $zoom;
        return $this;
    }

    /**
     * Retrieves zoom.
     *
     * @return int
     */
    public function getZoom()
    {
        return $this->zoom;
    }

    /**
     * Sets zoomMax.
     *
     * @param int $zoomMax
     * @return $this
     */
    public function setZoomMax($zoomMax)
    {
        $this->zoomMax = $zoomMax;
        return $this;
    }

    /**
     * Retrieves zoomMax.
     *
     * @return int
     */
    public function getZoomMax()
    {
        return $this->zoomMax;
    }

    /**
     * Sets exact.
     *
     * @param boolean $exact
     * @return $this
     */
    public function setExact($exact)
    {
        $this->exact = $exact;
        return $this;
    }

    /**
     * Retrieves exact.
     *
     * @return boolean
     */
    public function getExact()
    {
        return $this->exact;
    }

    /**
     * Sets isValid.
     *
     * @param boolean $isValid
     * @return $this
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;
        return $this;
    }

    /**
     * Retrieves isValid.
     *
     * @return boolean
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * Sets type.
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Retrieves type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

}
