<?php

namespace Backend\Modules\Groups\Entity;

use Doctrine\ORM\Mapping as ORM;
use Backend\Core\Entity\Module;

/**
 * This is the ModuleRight Entity
 *
 * @author Mathias Dewelde <mathias@dewelde.be>
 *
 * @ORM\Entity
 * @ORM\Table(name="GroupRightModule") //@todo Rename
 */
class ModuleRight
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
     * @var Group
     *
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="moduleRights")
     * @ORM\JoinColumn(name="groupId", referencedColumnName="id")
     */
    private $group;

    /**
     * @var Module
     *
     * @ORM\ManyToOne(targetEntity="\Backend\Core\Entity\Module")
     * @ORM\JoinColumn(name="moduleId", referencedColumnName="id")
     */
    private $module;

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
     * Set module
     *
     * @param Module $module
     * @return ActionRight
     */
    public function setModule(Module $module = null)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set group
     *
     * @param Group $group
     * @return ModuleRight
     */
    public function setGroup(Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }
}
