<?php

namespace Backend\Modules\Profiles\Domain\Profile;

use Backend\Modules\Profiles\Domain\GroupRight\GroupRight;
use Backend\Modules\Profiles\Domain\Setting\Setting;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ProfilesProfile")
 * @ORM\Entity(repositoryClass="ProfileRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Profile
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
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var Status
     *
     * @ORM\Column(type="profiles_status")
     */
    private $status;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $displayName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $url;

    /**
     * @var Collection<GroupRight>
     *
     * @ORM\OneToMany(
     *     targetEntity="Backend\Modules\Profiles\Domain\GroupRight\GroupRight",
     *     mappedBy="profile"
     * )
     */
    private $rights;

    /**
     * @var Collection<Setting>
     *
     * @ORM\OneToMany(
     *     targetEntity="Backend\Modules\Profiles\Domain\Setting\Setting",
     *     mappedBy="profile",
     *     cascade={"persist","remove"}
     * )
     */
    private $settings;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $registeredOn;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $editedOn;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    public function __construct(
        string $email,
        string $password,
        Status $status,
        ?string $displayName,
        ?string $url
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->status = $status;
        $this->displayName = $displayName;
        $this->url = $url;

        $this->rights = new ArrayCollection();
        $this->settings = new ArrayCollection();
    }

    public function update(
        string $email,
        string $password,
        Status $status,
        ?string $displayName,
        ?string $url
    ): void {
        $this->email = $email;
        $this->password = $password;
        $this->status = $status;
        $this->displayName = $displayName;
        $this->url = $url;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getRights(): Collection
    {
        return $this->rights;
    }

    public function addRight(GroupRight $groupRight): void
    {
        $this->rights->add($groupRight);
    }

    public function getSettings(): Collection
    {
        return $this->settings;
    }

    public function getSetting(string $name): ?string
    {
        $foundSetting = $this->settings->filter(
            function (Setting $setting) use ($name) {
                return $setting->getName() === $name;
            }
        );

        if ($foundSetting->isEmpty()) {
            return null;
        }

        return $foundSetting->first()->getValue();
    }

    public function addSetting(Setting $setting): void
    {
        $this->settings->add($setting);
    }

    public function setSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->setSetting($key, $value);
        }
    }

    public function setSetting(string $name, ?string $value): void
    {
        $foundSetting = $this->settings->filter(
            function (Setting $setting) use ($name) {
                return $setting->getName() === $name;
            }
        );

        if ($foundSetting->isEmpty()) {
            $this->settings->add(new Setting($this, $name, $value));

            return;
        }

        $foundSetting->first()->update($value);
    }

    public function getRegisteredOn(): DateTime
    {
        return $this->registeredOn;
    }

    public function getEditedOn(): DateTime
    {
        return $this->editedOn;
    }

    public function getLastLogin(): DateTime
    {
        return $this->lastLogin;
    }

    public function activate(): void
    {
        $this->status = Status::active();
    }

    public function inactivate(): void
    {
        $this->status = Status::inactive();
    }

    public function block(): void
    {
        $this->status = Status::blocked();
    }

    public function delete(): void
    {
        $this->status = Status::deleted();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        $this->registeredOn = $this->editedOn = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->editedOn = new DateTime();
    }

    public function registerLogin(): void
    {
        $this->lastLogin = new DateTime();
    }

    public function isInGroup(int $groupId): bool
    {
        $foundGroups = $this->getRights()->filter(
            function (GroupRight $groupRight) use ($groupId) {
                return $groupRight->getGroup()->getId() === $groupId;
            }
        );

        return !$foundGroups->isEmpty();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'status' => (string) $this->status,
            'display_name' => $this->displayName,
            'url' => $this->url,
        ];
    }
}
