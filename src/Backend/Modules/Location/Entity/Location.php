<?php

namespace Backend\Modules\Location\Entity;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the Location Entity
 *
 * @author Mathias Dewelde <mathias@studiorauw.be>
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="location")
 */
class Location
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $language;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="extra_id")
     */
    private $extraId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $lat;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $lng;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="show_overview")
     */
    private $showOverview = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on")
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="edited_on")
     */
    private $editedOn;


    /**
     *  @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdOn = new \Datetime();
        $this->editedOn = new \Datetime();
    }

    /**
     *  @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->editedOn = new \Datetime();
    }

    /**
     * Set id
     *
     * @param  integer      $id
     * @return Location
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set language
     *
     * @param  string       $language
     * @return Location
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set extraId
     *
     * @param  integer      $extraId
     * @return Location
     */
    public function setExtraId($extraId)
    {
        $this->extraId = $extraId;

        return $this;
    }

    /**
     * Get extraId
     *
     * @return integer
     */
    public function getExtraId()
    {
        return $this->extraId;
    }

    /**
     * Set title
     *
     * @param  string       $title
     * @return Location
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set street
     *
     * @param  string       $street
     * @return Location
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set number
     *
     * @param  string       $number
     * @return Location
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set zip
     *
     * @param  string       $zip
     * @return Location
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set city
     *
     * @param  string       $city
     * @return Location
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param  string       $country
     * @return Location
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set lat
     *
     * @param  string       $lat
     * @return Location
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param  string       $lng
     * @return Location
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set show overview
     *
     * @param  boolean      $showOverview
     * @return Location
     */
    public function setShowOverview($showOverview)
    {
        $this->showOverview = $showOverview;

        return $this;
    }

    /**
     * Get show overview
     *
     * @return string
     */
    public function getShowOverview()
    {
        return $this->showOverview;
    }

    /**
     * Set createdOn
     *
     * @param  \DateTime    $createdOn
     * @return Location
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set editedOn
     *
     * @param  \DateTime    $editedOn
     * @return Location
     */
    public function setEditedOn($editedOn)
    {
        $this->editedOn = $editedOn;

        return $this;
    }

    /**
     * Get editedOn
     *
     * @return \DateTime
     */
    public function getEditedOn()
    {
        return $this->editedOn;
    }

    /**
     * Get the location as an array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'language' => $this->getLanguage(),
            'extra_id' => $this->getExtraId(),
            'title' => $this->getTitle(),
            'street' => $this->getStreet(),
            'number' => $this->getNumber(),
            'zip' => $this->getZip(),
            'city' => $this->getCity(),
            'country' => $this->getCountry(),
            'lat' => $this->getLat(),
            'lng' => $this->getLng(),
            'show_overview' => $this->getShowOverview(),
            'created_on' => $this->getCreatedOn(),
            'edited_on' => $this->getEditedOn()
        );
    }
}
