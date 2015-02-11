<?php

namespace Backend\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the Meta Entity
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 *
 * @todo Remove the custom table name when our old meta table isn't used anymore
 * @ORM\Entity
 * @ORM\Table(name="MetaDoctrine",indexes={@ORM\Index(name="idx_url", columns={"url"})})
 */
class Meta
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
    private $keywords;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $overwriteKeywords = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $overwriteDescription = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $overwriteTitle = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $overwriteUrl = false;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $custom;

    /**
     * @var string
     *
     * @todo don't save this data in a serialized text field
     * @ORM\Column(type="text", nullable=true)
     */
    private $data;

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
     * Set keywords
     *
     * @param string $keywords
     * @return Meta
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set overwriteKeywords
     *
     * @param boolean $overwriteKeywords
     * @return Meta
     */
    public function setOverwriteKeywords($overwriteKeywords)
    {
        $this->overwriteKeywords = $overwriteKeywords;

        return $this;
    }

    /**
     * Get overwriteKeywords
     *
     * @return boolean
     */
    public function getOverwriteKeywords()
    {
        return $this->overwriteKeywords;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Meta
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set overwriteDescription
     *
     * @param boolean $overwriteDescription
     * @return Meta
     */
    public function setOverwriteDescription($overwriteDescription)
    {
        $this->overwriteDescription = $overwriteDescription;

        return $this;
    }

    /**
     * Get overwriteDescription
     *
     * @return boolean
     */
    public function getOverwriteDescription()
    {
        return $this->overwriteDescription;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Meta
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
     * Set overwriteTitle
     *
     * @param boolean $overwriteTitle
     * @return Meta
     */
    public function setOverwriteTitle($overwriteTitle)
    {
        $this->overwriteTitle = $overwriteTitle;

        return $this;
    }

    /**
     * Get overwriteTitle
     *
     * @return boolean
     */
    public function getOverwriteTitle()
    {
        return $this->overwriteTitle;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Meta
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set overwriteUrl
     *
     * @param boolean $overwriteUrl
     * @return Meta
     */
    public function setOverwriteUrl($overwriteUrl)
    {
        $this->overwriteUrl = $overwriteUrl;

        return $this;
    }

    /**
     * Get overwriteUrl
     *
     * @return boolean
     */
    public function getOverwriteUrl()
    {
        return $this->overwriteUrl;
    }

    /**
     * Set custom
     *
     * @param string $custom
     * @return Meta
     */
    public function setCustom($custom)
    {
        $this->custom = $custom;

        return $this;
    }

    /**
     * Get custom
     *
     * @return string
     */
    public function getCustom()
    {
        return $this->custom;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return Meta
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
}
