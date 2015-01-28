<?php

namespace Backend\Modules\Groups\Entity;

use Doctrine\ORM\Mapping as ORM;
use Backend\Core\Entity\Module;

/**
 * This is the ActionRight Entity
 *
 * @author Mathias Dewelde <mathias@dewelde.be>
 *
 * @ORM\Entity
 * @ORM\Table(name="GroupRightAction") //@todo Rename
 */
class ActionRight
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
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="actionRights")
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
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $level = 1;

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
     * Set action
     *
     * @param string $action
     * @return ActionRight
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set level
     *
     * @param string $level
     * @return ActionRight
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set group
     *
     * @param Group $group
     * @return ActionRight
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
