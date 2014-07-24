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
     * @var string
     *
     * @ORM\Column(name="url_sphere", type="string", length=255, nullable=false)
     */
    private $urlSphere;

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
}