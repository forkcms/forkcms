<?php

namespace Backend\Modules\Groups\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This is the Group Entity
 *
 * @author Mathias Dewelde <mathias@dewelde.be>
 *
 * @ORM\Entity
 * @ORM\Table(name="ForkGroup") //@todo Rename, but group is a reserved keyword
 */
class Group
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ModuleRight", mappedBy="group")
     */
    private $moduleRights;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ActionRight", mappedBy="group")
     */
    private $actionRights;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var Array
     *
     * @ORM\Column(type="array")
     **/
    private $parameters;

    /**
     * @var Array
     *
     * @ORM\Column(type="array")
     **/
    private $settings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->moduleRights = new ArrayCollection();
        $this->actionRights = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Group
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
     * Set parameters
     *
     * @param array $parameters
     * @return Group
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return array 
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set settings
     *
     * @param array $settings
     * @return Group
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Get settings
     *
     * @return array 
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Add moduleRights
     *
     * @param ModuleRight $moduleRights
     * @return Group
     */
    public function addModuleRight(ModuleRight $moduleRights)
    {
        $this->moduleRights[] = $moduleRights;

        return $this;
    }

    /**
     * Remove moduleRights
     *
     * @param ModuleRight $moduleRights
     */
    public function removeModuleRight(ModuleRight $moduleRights)
    {
        $this->moduleRights->removeElement($moduleRights);
    }

    /**
     * Get moduleRights
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModuleRights()
    {
        return $this->moduleRights;
    }

    /**
     * Add actionRights
     *
     * @param ActionRight $actionRights
     * @return Group
     */
    public function addActionRight(ActionRight $actionRights)
    {
        $this->actionRights[] = $actionRights;

        return $this;
    }

    /**
     * Remove actionRights
     *
     * @param ActionRight $actionRights
     */
    public function removeActionRight(ActionRight $actionRights)
    {
        $this->actionRights->removeElement($actionRights);
    }

    /**
     * Get actionRights
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActionRights()
    {
        return $this->actionRights;
    }
}
