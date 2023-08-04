<?php

namespace Backend\Modules\Profiles\Domain\GroupRight;

use Backend\Modules\Profiles\Domain\Profile\Profile;
use Backend\Modules\Profiles\Domain\Group\Group;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *      name="ProfilesGroupRight",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"profile_id", "group_id"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="Backend\Modules\Profiles\Domain\GroupRight\GroupRightRepository")
 * @ORM\HasLifecycleCallbacks
 */
class GroupRight
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
     * @var Profile|null
     *
     * @ORM\ManyToOne(targetEntity="Backend\Modules\Profiles\Domain\Profile\Profile", inversedBy="rights")
     */
    private $profile;

    /**
     * @var Group|null
     *
     * @ORM\ManyToOne(targetEntity="Backend\Modules\Profiles\Domain\Group\Group", inversedBy="rights")
     */
    private $group;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startsOn;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiresOn;

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

    public function __construct(
        Profile $profile,
        Group $group,
        ?DateTime $startsOn,
        ?DateTime $expiresOn
    ) {
        $this->profile = $profile;
        $this->group = $group;
        $this->startsOn = $startsOn;
        $this->expiresOn = $expiresOn;

        $profile->addRight($this);
        $group->addRight($this);
    }

    public function update(Group $group, DateTime $startsOn, ?DateTime $expiresOn): void
    {
        $this->group = $group;
        $this->startsOn = $startsOn;
        $this->expiresOn = $expiresOn;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProfile(): Profile
    {
        return $this->profile;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startsOn;
    }

    public function getExpiryDate(): ?DateTime
    {
        return $this->expiresOn;
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
        $expiresOnTimestamp = null;
        if ($this->getExpiryDate() instanceof DateTime) {
            $expiresOnTimestamp = $this->getExpiryDate()->getTimestamp();
        }

        return [
            'id' => $this->getId(),
            'profile_id' => $this->getProfile()->getId(),
            'group_id' => $this->getGroup()->getId(),
            'name' => $this->getGroup()->getName(),
            'expires_on' => $expiresOnTimestamp,
        ];
    }
}
