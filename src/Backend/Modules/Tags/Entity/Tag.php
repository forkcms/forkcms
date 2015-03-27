<?php

namespace Backend\Modules\Tags\Entity;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the Tag Entity
 *
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 *
 * @ORM\Entity
 * @ORM\Table(name="tags")
 */
class Tag
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
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $tag;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=11)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity="Backend\Modules\Tags\Entity\TagConnection", mappedBy="tag")
     */
    private $connections;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->connections = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Gets id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of language.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets the value of language.
     *
     * @param string $language the language
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Gets the value of tag.
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Sets the value of tag.
     *
     * @param string $tag the tag
     * @return self
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Gets the value of number.
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Sets the value of number.
     *
     * @param string $number the number
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Gets the value of url.
     *
     * @return integer
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the value of url.
     *
     * @param string $number the url
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Add connections
     *
     * @param \Backend\Modules\Tags\Entity\TagConnection $connections
     * @return Category
     */
    public function addConnection(\Backend\Modules\Tags\Entity\TagConnection $connections)
    {
        $this->connections[] = $connections;

        return $this;
    }

    /**
     * Remove connections
     *
     * @param \Backend\Modules\Tags\Entity\TagConnection $connections
     */
    public function removeConnection(\Backend\Modules\Tags\Entity\TagConnection $connections)
    {
        $this->connections->removeElement($connections);
    }

    /**
     * Get connections
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConnections()
    {
        return $this->connections;
    }
}
