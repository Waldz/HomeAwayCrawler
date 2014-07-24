<?php

namespace FlatFindr\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="listing_photo"
 * )
 */
class ListingPhoto
{

    /**
     * @var Listing
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="FlatFindr\Entity\Listing", inversedBy="photoList")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $listing;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="url_secure", type="string", length=255, nullable=true)
     */
    private $urlSecure;

    /**
     * @var int
     *
     * @ORM\Column(name="`order`", type="integer", length=2, nullable=true)
     */
    private $order;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $size;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notes;

    /**
     * Sets listing.
     *
     * @param \FlatFindr\Entity\Listing $listing
     */
    public function setListing($listing)
    {
        $this->listing = $listing;
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
     * Sets url.
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Retrieves url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets urlSecure.
     *
     * @param string $url
     * @return $this
     */
    public function setUrlSecure($url)
    {
        $this->urlSecure = $url;
        return $this;
    }

    /**
     * Retrieves urlSecure.
     *
     * @return string
     */
    public function getUrlSecure()
    {
        return $this->urlSecure;
    }

    /**
     * Sets order.
     *
     * @param int $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Retrieves order.
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Sets width.
     *
     * @param int $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Retrieves width.
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Sets height.
     *
     * @param int $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Retrieves height.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Sets size.
     *
     * @param string $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Retrieves size.
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
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
     * Sets note.
     *
     * @param string $note
     * @return $this
     */
    public function setNotes($note)
    {
        $this->notes = $note;
        return $this;
    }

    /**
     * Retrieves note.
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

}
