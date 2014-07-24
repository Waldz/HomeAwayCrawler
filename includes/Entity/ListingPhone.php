<?php

namespace FlatFindr\Entity;

use Doctrine\ORM\Mapping as ORM;
use FlatFindr\Entity\Listing;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="listing_phone"
 * )
 */
class ListingPhone
{

    const TYPE_PRIMARY = 'primary';
    const TYPE_SECONDARY = 'secontary';

    /**
     * @var Listing
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="FlatFindr\Entity\Listing", inversedBy="phoneList")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $listing;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="country_code", type="string", length=3, nullable=true)
     */
    private $countryCode;

    /**
     * @var string
     *
     * @ORM\Column(name="extension_code", type="string", length=10, nullable=true)
     */
    private $extensionCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $notes;

    /**
     * Sets listing.
     *
     * @param Listing $listing
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
     * @return Listing
     */
    public function getListing()
    {
        return $this->listing;
    }

    /**
     * Sets countryCode.
     *
     * @param string $countryCode
     * @return $this
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * Retrieves countryCode.
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Sets extensionCode.
     *
     * @param string $extensionCode
     * @return $this
     */
    public function setExtensionCode($extensionCode)
    {
        $this->extensionCode = $extensionCode;
        return $this;
    }

    /**
     * Retrieves extensionCode.
     *
     * @return string
     */
    public function getExtensionCode()
    {
        return $this->extensionCode;
    }

    /**
     * Sets phone.
     *
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Retrieves phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Retrieves phone.
     *
     * @return string
     */
    public function getPhoneFormatted()
    {
        $phone = str_replace(
            array('(', ')', '-', ' '),
            '',
            $this->getPhone()
        );
        if($countryCode=$this->getCountryCode()) {
            return sprintf('+%s-%s', $countryCode, $phone);
        } elseif($extensionCode=$this->getExtensionCode()) {
            return sprintf('+%s-%s', $extensionCode, $phone);
        } else {
            return $phone;
        }
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
