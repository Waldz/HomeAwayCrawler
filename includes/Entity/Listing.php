<?php

namespace FlatFindr\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Listing
 *
 * @package HomeAway
 * @author Valdas Petrulis <petrulis.valdas@gmail.com>
 *
 * @ORM\Entity()
 * @ORM\Table(
 *     name="listing",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="provider_ident", columns={"provider","provider_ident"})
 *     }
 * )
 */
class Listing
{

    const PROVIDER_HOMEAWAY = 'HomeAway';

    const STATUS_NEW = 'new';
    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $provider;

    /**
     * @var string
     *
     * @ORM\Column(name="provider_ident", type="string", length=255, nullable=false)
     */
    private $providerIdent;

    /**
     * @var string
     *
     * @ORM\Column(name="url_detail", type="string", length=255, nullable=false)
     */
    private $urlDetail;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=1000, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="url_sphere", type="string", length=255, nullable=false)
     */
    private $urlSphere;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=10, nullable=true)
     */
    private $status = self::STATUS_NEW;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_sync", type="datetime", nullable=true)
     */
    private $dateSync;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_next_sync", type="datetime", nullable=true)
     */
    private $dateNextSync;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=1000, nullable=true)
     */
    private $jsonData;

    /**
     * All phones of this listing
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FlatFindr\Entity\ListingPhone", mappedBy="listing", cascade={"all"})
     * @ORM\JoinTable(
     *   name="listing_phone",
     *   joinColumns={@ORM\JoinColumn(name="listing_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    public $phoneList;

    /**
     * All photos of this listing
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FlatFindr\Entity\ListingPhoto", mappedBy="listing", cascade={"all"})
     * @ORM\JoinTable(
     *   name="listing_photo",
     *   joinColumns={@ORM\JoinColumn(name="listing_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    public $photoList;

    /**
     * All locations of this listing
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FlatFindr\Entity\ListingLocation", mappedBy="listing", cascade={"all"})
     * @ORM\JoinTable(
     *   name="listing_location",
     *   joinColumns={@ORM\JoinColumn(name="listing_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    public $locationList;

    /**
     * All prices of this listing
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FlatFindr\Entity\ListingPrice", mappedBy="listing", cascade={"all"})
     * @ORM\JoinTable(
     *   name="listing_price",
     *   joinColumns={@ORM\JoinColumn(name="listing_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    public $priceList;

    /**
     * All amenities of this listing
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FlatFindr\Entity\ListingAmenity", mappedBy="listing", cascade={"all"})
     * @ORM\JoinTable(
     *   name="listing_amenitiy",
     *   joinColumns={@ORM\JoinColumn(name="listing_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    public $amenityList;

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
     * Sets provider.
     *
     * @param string $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * Retrieves provider.
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Sets providerIdent.
     *
     * @param string $providerIdent
     */
    public function setProviderIdent($providerIdent)
    {
        $this->providerIdent = $providerIdent;
    }

    /**
     * Retrieves providerIdent.
     *
     * @return string
     */
    public function getProviderIdent()
    {
        return $this->providerIdent;
    }

    /**
     * Sets urlDetail.
     *
     * @param string $urlDetail
     */
    public function setUrlDetail($urlDetail)
    {
        $this->urlDetail = $urlDetail;
    }

    /**
     * Retrieves urlDetail.
     *
     * @return string
     */
    public function getUrlDetail()
    {
        return $this->urlDetail;
    }

    /**
     * Sets title.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Retrieves title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Retrieves description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets owner.
     *
     * @param string $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * Retrieves owner.
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Sets urlSphere.
     *
     * @param string $urlSphere
     */
    public function setUrlSphere($urlSphere)
    {
        $this->urlSphere = $urlSphere;
    }

    /**
     * Retrieves urlSphere.
     *
     * @return string
     */
    public function getUrlSphere()
    {
        return $this->urlSphere;
    }

    /**
     * Sets jsonData.
     *
     * @param string $jsonData
     */
    public function setJsonData($jsonData)
    {
        $this->jsonData = $jsonData;
    }

    /**
     * Retrieves jsonData.
     *
     * @return string
     */
    public function getJsonData()
    {
        return $this->jsonData;
    }

    /**
     * Sets status.
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Retrieves status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets dateSync.
     *
     * @param \DateTime $dateSync
     * @return $this
     */
    public function setDateSync($dateSync)
    {
        $this->dateSync = $dateSync;
        return $this;
    }

    /**
     * Retrieves dateSync.
     *
     * @return \DateTime
     */
    public function getDateSync()
    {
        return $this->dateSync;
    }

    /**
     * Sets dateNextSync.
     *
     * @param \DateTime $dateNextSync
     * @return $this
     */
    public function setDateNextSync($dateNextSync)
    {
        $this->dateNextSync = $dateNextSync;
        return $this;
    }

    /**
     * Sets dateNextSync (after first fail) in manner: +1d, +2d, +4d, +7d, +7d, +7d
     */
    public function setDateNextSyncAfterFail()
    {
        $now = new \DateTime('now');
        $diff = $this->getDateSync()->diff($now);

        // First fail
        if($diff->d==0) {
            $this->setDateNextSync(
                $now->add(new \DateInterval('P1D'))
            );
        // Max check frequency is 7days
        } elseif($diff->d>=4) {
            $this->setDateNextSync(
                $now->add(new \DateInterval('P7D'))
            );
        // Make next checks more rare
        } else {
            $this->setDateNextSync(
                $now->add(new \DateInterval('P'.(2*$diff->d).'D'))
            );
        }
    }

    /**
     * Retrieves dateNextSync.
     *
     * @return \DateTime
     */
    public function getDateNextSync()
    {
        return $this->dateNextSync;
    }

    /**
     * Sets phoneList.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $phoneList
     */
    public function setPhoneList($phoneList)
    {
        $this->phoneList = $phoneList;
    }

    /**
     * Retrieves phoneList.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPhoneList()
    {
        return $this->phoneList;
    }

    /**
     * @param $phoneNumber
     * @return ListingPhone
     */
    public function getPhone($phoneNumber)
    {
        foreach($this->getPhoneList() as $phone) {
            /** @var ListingPhone $phone */
            if($phone->getPhone()==$phoneNumber) {
                return $phone;
            }
        }

        return null;
    }

    /**
     * Adds phone.
     *
     * @param ListingPhone $phone
     */
    public function addPhone($phone)
    {
        $phone->setListing($this);
        $this->getPhoneList()->add($phone);
    }

    /**
     * Retrieves main phone.
     *
     * @return ListingPhone
     */
    public function getPhonePrimary()
    {
        $list = $this->getPhoneList();

        foreach($list as $phone) {
            /** @var ListingPhone $phone */
            if($phone->getType()==ListingPhone::TYPE_PRIMARY) {
                return $phone;
            }
        }

        if($list->count()>0) {
            return $list->get(0);
        }

        return null;
    }

    /**
     * Sets photoList.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $photoList
     */
    public function setPhotoList($photoList)
    {
        $this->photoList = $photoList;
    }

    /**
     * Retrieves photoList.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPhotoList()
    {
        return $this->photoList;
    }

    /**
     * @param $url
     * @return ListingPhoto
     */
    public function getPhoto($url)
    {
        foreach($this->getPhotoList() as $photo) {
            /** @var ListingPhoto $photo */
            if($photo->getUrl()==$url) {
                return $photo;
            }
        }

        return null;
    }

    /**
     * Adds photo.
     *
     * @param ListingPhoto $photo
     */
    public function addPhoto($photo)
    {
        $photo->setListing($this);
        $this->getPhotoList()->add($photo);
    }

    /**
     * Sets locationList.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $locationList
     */
    public function setLocationList($locationList)
    {
        $this->locationList = $locationList;
    }

    /**
     * Retrieves locationList.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLocationList()
    {
        return $this->locationList;
    }

    /**
     * @param double $latitude
     * @param double $longitude
     *
     * @return ListingLocation
     */
    public function getLocation($latitude, $longitude)
    {
        foreach($this->getLocationList() as $location) {
            /** @var ListingLocation $location */
            if(
                $location->getLatitude()==$latitude
                && $location->getLongitude()==$longitude
            ) {
                return $location;
            }
        }

        return null;
    }

    /**
     * Adds location.
     *
     * @param ListingLocation $location
     */
    public function addLocation($location)
    {
        $location->setListing($this);
        $this->getLocationList()->add($location);
    }

    /**
     * Sets priceList.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $priceList
     */
    public function setPriceList($priceList)
    {
        $this->priceList = $priceList;
    }

    /**
     * Retrieves priceList.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPriceList()
    {
        return $this->priceList;
    }

    /**
     * @param string $type
     * @param \DateTime $dateStart
     * @param \DateTime $dateEnd
     *
     * @return ListingPrice
     */
    public function getPrice($type, \DateTime $dateStart, \DateTime $dateEnd)
    {
        foreach($this->getPriceList() as $listingPrice) {
            /** @var ListingPrice $listingPrice */
            if(
                $listingPrice->getType()==$type
                && $listingPrice->getDateStart()->format('Y-m-d')==$dateStart->format('Y-m-d')
                && $listingPrice->getDateEnd()->format('Y-m-d')==$dateEnd->format('Y-m-d')
            ) {
                return $listingPrice;
            }
        }

        return null;
    }

    /**
     * Adds price.
     *
     * @param ListingPrice $price
     */
    public function addPrice($price)
    {
        $price->setListing($this);
        $this->getPriceList()->add($price);
    }

    /**
     * Sets amenityList.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $amenityList
     */
    public function setAmenityList($amenityList)
    {
        $this->amenityList = $amenityList;
    }

    /**
     * Retrieves amenityList.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAmenityList()
    {
        return $this->amenityList;
    }

    /**
     * Adds amenity.
     *
     * @param ListingAmenity $amenity
     */
    public function addAmenity($amenity)
    {
        $amenity->setListing($this);
        $this->getAmenityList()->add($amenity);
    }
}