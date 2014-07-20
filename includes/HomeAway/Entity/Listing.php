<?php

/**
 * Class Listing
 *
 * @package HomeAway
 * @author Valdas Petrulis <petrulis.valdas@gmail.com>
 */
class Listing
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $urlDetail;

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
}