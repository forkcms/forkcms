<?php

namespace Backend\Modules\Profiles\Domain\ProfileGroup;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ProfilesProfileGroup")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ProfileGroup
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
     * @var Collection;
     *
     * @ORM\OneToMany(
     *     targetEntity="Backend\Modules\Profiles\Domain\ProfileGroupRight\ProfileGroupRight",
     *     mappedBy="profile"
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

    public static function fromDataTransferObject(ProfileGroupDataTransferObject $dataTransferObject): self
    {
        return self::create($dataTransferObject);
    }

    private static function create(ProfileGroupDataTransferObject $dataTransferObject): self
    {
        return new self(
            $dataTransferObject->name
        );
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
}
