<?php

namespace Backend\Modules\Profiles\Domain\ProfileSetting;

use Backend\Modules\Profiles\Domain\Profile\Profile;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ProfileSetting
{
    /**
     * @var Profile
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Backend\Modules\Profiles\Domain\Profile\Profile", inversedBy="settings")
     */
    private $profile;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="object")
     */
    private $value;

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

    public function __construct(Profile $profile, string $name, string $value)
    {
        $this->profile = $profile;
        $this->name = $name;
        $this->value = $value;
    }

    public function getProfile(): Profile
    {
        return $this->profile;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
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
