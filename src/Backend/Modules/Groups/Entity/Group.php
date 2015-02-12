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
 * @ORM\Table(name="Groups")
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
     * @ORM\OneToMany(targetEntity="GroupModuleRight", mappedBy="group", cascade={"persist", "remove"})
     */
    private $allowedModules;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GroupActionRight", mappedBy="group", cascade={"persist", "remove"})
     */
    private $allowedActions;

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
        $this->allowedModules = new ArrayCollection();
        $this->allowedActions = new ArrayCollection();
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
     * Add allowedModules
     *
     * @param GroupModuleRight $allowedModules
     * @return Group
     */
    public function addAllowedModule(GroupModuleRight $allowedModules)
    {
        $this->allowedModules[] = $allowedModules;

        return $this;
    }

    /**
     * Remove allowedModules
     *
     * @param GroupModuleRight $allowedModules
     */
    public function removeAllowedModule(GroupModuleRight $allowedModules)
    {
        $this->allowedModules->removeElement($allowedModules);
    }

    /**
     * Get allowedModules
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAllowedModules()
    {
        return $this->allowedModules;
    }

    /**
     * Add allowedActions
     *
     * @param GroupActionRight $allowedActions
     * @return Group
     */
    public function addAllowedAction(GroupActionRight $allowedActions)
    {
        $this->allowedActions[] = $allowedActions;

        return $this;
    }

    /**
     * Remove allowedActions
     *
     * @param GroupActionRight $allowedActions
     */
    public function removeAllowedAction(GroupActionRight $allowedActions)
    {
        $this->allowedActions->removeElement($allowedActions);
    }

    /**
     * Get allowedActions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAllowedActions()
    {
        return $this->allowedActions;
    }
}
