<?php

namespace Backend\Modules\Extensions\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the Module Entity
 *
 * @author Mathias Dewelde <mathias@dewelde.be>
 *
 * @ORM\Entity(repositoryClass="Backend\Modules\Extensions\Entity\ModuleRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="Module")
 */
class Module
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
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $installedOn;

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
     * Set name
     *
     * @param string $name
     * @return Module
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *  @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->installedOn = new \Datetime();
    }

    /**
     * Set installedOn
     *
     * @param \DateTime $installedOn
     * @return Module
     */
    public function setInstalledOn($installedOn)
    {
        $this->installedOn = $installedOn;

        return $this;
    }

    /**
     * Get installedOn
     *
     * @return \DateTime 
     */
    public function getInstalledOn()
    {
        return $this->installedOn;
    }
}
