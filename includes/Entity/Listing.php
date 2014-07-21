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
}