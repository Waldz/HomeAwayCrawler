<?php

namespace FlatFindr\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="listing_amenity"
 * )
 */
class ListingAmenity
{

    const NAME_PROPERTY_TYPE = 'property_type';
    const NAME_BUILDING_TYPE = 'building_type';
    const NAME_LOCATION_TYPE = 'location_type';
    const NAME_PAYMENT_TYPE = 'payment_type';
    const NAME_ACCOMMODATION_TYPE = 'accommodation_type';
    const NAME_AREA = 'area';
    const NAME_THEME = 'theme';
    const NAME_GENERAL = 'general';
    const NAME_KITCHEN = 'kitchen';
    const NAME_DINING = 'dining';
    const NAME_BATHROOM = 'bathroom';
    const NAME_BEDROOM = 'bedroom';
    const NAME_ENTERTAINMENT = 'entertainment';
    const NAME_COMMUNICATIONS = 'communications';
    const NAME_OUTSIDE = 'outside';
    const NAME_SUITABILITY = 'suitability';
    const NAME_POOL = 'pool';
    const NAME_ATTRACTIONS = 'attractions';
    const NAME_LEISURE = 'activities';
    const NAME_SERVICES = 'services';
    const NAME_SERVICES_ONSITE = 'services_onsite';
    const NAME_SPORTS = 'sports';
    const NAME_MEALS = 'meals';
    const NAME_NOTES = 'notes';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Listing
     *
     * @ORM\ManyToOne(targetEntity="FlatFindr\Entity\Listing", inversedBy="locationList")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $listing;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * Sets id.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Retrieves id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

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
     * Sets name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Retrieves name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets value.
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Retrieves value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}
