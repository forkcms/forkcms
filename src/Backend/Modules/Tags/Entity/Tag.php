<?php

namespace Backend\Modules\Tags\Entity;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @ORM\Column(type="string", length=5)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

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
     * @ORM\OneToMany(
     *     targetEntity="Backend\Modules\Tags\Entity\TagConnection",
     *     mappedBy="tag",
     *     cascade={"persist", "remove"}
     * )
     */
    private $connections;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->connections = new ArrayCollection();
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
     * @return string
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param string $name the name
     * @return Tag
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return Tag
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
     * @return Tag
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Add a connection
     *
     * @param TagConnection $connection
     * @return Tag
     */
    public function addConnection(TagConnection $connection)
    {
        $this->connections[] = $connection;
        $connection->setTag($this);

        return $this;
    }

    /**
     * Remove connection
     *
     * @param TagConnection $connection
     */
    public function removeConnection(TagConnection $connection)
    {
        $this->connections->removeElement($connection);

        return $this;
    }

    /**
     * Get connections
     *
     * @return Collection
     */
    public function getConnections()
    {
        return $this->connections;
    }
}
