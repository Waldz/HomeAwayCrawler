<?php

namespace FlatFindr\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="listing_price",
 *   uniqueConstraints={
 *       @ORM\UniqueConstraint(name="unique_price", columns={"listing_id","type","date_start","date_end"})
 *   }
 * )
 */
class ListingPrice
{

    const TYPE_DAILY = 'daily';
    const TYPE_WEEKEND = 'daily_weekend';
    const TYPE_WEEKLY = 'weekly';
    const TYPE_MONTHLY = 'monthly';
    const TYPE_EVENT = 'event';

    const BASIS_OVERAL = 'overal';
    const BASIS_PERSON = 'person';

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
     * @ORM\Column(name="type", type="string", length=10, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="basis", type="string", length=10, nullable=false)
     */
    private $basis;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_start", type="date", nullable=false)
     */
    private $dateStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end", type="date", nullable=false)
     */
    private $dateEnd;

    /**
     * @var double
     *
     * @ORM\Column(name="price", type="decimal", scale=2, precision=8, nullable=false)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=false)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notes;

    /**
     * Sets id.
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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

    /**
     * Sets basis.
     *
     * @param string $basis
     * @return $this
     */
    public function setBasis($basis)
    {
        $this->basis = $basis;
        return $this;
    }

    /**
     * Retrieves basis.
     *
     * @return string
     */
    public function getBasis()
    {
        return $this->basis;
    }

    /**
     * Sets dateStart.
     *
     * @param \DateTime $dateStart
     * @return $this
     */
    public function setDateStart($dateStart)
    {
        //$this->dateStart = $dateStart->format('Y-m-d');
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * Retrieves dateStart.
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Sets dateEnd.
     *
     * @param \DateTime $dateEnd
     * @return $this
     */
    public function setDateEnd($dateEnd)
    {
        //$this->dateEnd = $dateEnd->format('Y-m-d');
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * Retrieves dateEnd.
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Sets price.
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Retrieves price.
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets currency.
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Retrieves currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Sets notes.
     *
     * @param string $notes
     * @return $this
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * Retrieves notes.
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

}
