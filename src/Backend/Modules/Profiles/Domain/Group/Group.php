<?php

namespace Backend\Modules\Profiles\Domain\Group;

use Backend\Modules\Profiles\Domain\GroupRight\GroupRight;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ProfilesGroup")
 * @ORM\Entity(repositoryClass="GroupRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Group
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var Collection<GroupRight>;
     *
     * @ORM\OneToMany(
     *     targetEntity="Backend\Modules\Profiles\Domain\GroupRight\GroupRight",
     *     mappedBy="group",
     *     cascade={"persist", "remove"}
     * )
     */
    private $rights;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $editedOn;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->rights = new ArrayCollection();
    }

    public function update(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRights(): Collection
    {
        return $this->rights;
    }

    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

    public function getEditedOn(): DateTime
    {
        return $this->editedOn;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        $this->createdOn = $this->editedOn = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->editedOn = new DateTime();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }

    public function addRight(GroupRight $groupRight): void
    {
        $this->rights->add($groupRight);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
