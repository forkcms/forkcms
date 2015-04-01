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
 * This is the TagConnection Entity
 *
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 *
 * @ORM\Entity
 * @ORM\Table
 */
class TagConnection
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $module;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     */
    private $other_id;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Backend\Modules\Tags\Entity\Tag", inversedBy="connections")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id", nullable=false)
     */
    private $tag;

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
     * Gets the value of module.
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Sets the value of module.
     *
     * @param string $module the module
     * @return TagConnection
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Gets the value of otherId.
     *
     * @return string
     */
    public function getOtherId()
    {
        return $this->other_id;
    }

    /**
     * Sets the value of otherId.
     *
     * @param string $otherId the otherId
     * @return TagConnection
     */
    public function setOtherId($otherId)
    {
        $this->other_id = $otherId;

        return $this;
    }

    /**
     * Get tag
     *
     * @return Tag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set tag
     *
     * @param Tag $tag
     * @return TagConnection
     */
    public function setTag(Tag $tag)
    {
        $this->tag = $tag;

        return $this;
    }
}
