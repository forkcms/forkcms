<?php

namespace ForkCMS\Modules\Backend\Domain\User;

use Doctrine\Common\Collections\ArrayCollection;
use ForkCMS\Core\Domain\Doctrine\CollectionHelper;
use ForkCMS\Core\Domain\Form\Validator\UniqueDataTransferObject;
use ForkCMS\Core\Domain\Form\Validator\UniqueDataTransferObjectInterface;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroup;
use Symfony\Component\Validator\Constraints as Assert;

/** @implements UniqueDataTransferObjectInterface<User> */
#[UniqueDataTransferObject(['entityClass' => User::class, 'fields' => ['email'], 'message' => 'err.EmailExists'])]
#[UniqueDataTransferObject(['entityClass' => User::class, 'fields' => ['displayName'], 'message' => 'err.DisplayNameExists'])]
abstract class UserDataTransferObject implements UniqueDataTransferObjectInterface
{
    /**
     * @Assert\Email(message="err.EmailIsInvalid")
     * @Assert\NotBlank (message="err.EmailIsRequired")
     * @Assert\Length(max=180, maxMessage="err.EmailIsTooLong")
     */
    public ?string $email = null;

    /**
     * @Assert\NotBlank(message="err.PasswordIsRequired", groups={"create"})
     * @Assert\Length(minMessage="err.PasswordIsTooShort", min=12)
     * @Assert\NotCompromisedPassword(skipOnError="true")
     */
    public ?string $plainTextPassword = null;

    /**
     * @Assert\NotBlank (message="err.DisplayNameIsRequired")
     */
    public ?string $displayName = null;

    public bool $accessToBackend = true;

    public bool $superAdmin = false;

    protected ?User $userEntity;

    /** @var ArrayCollection<int|string,UserGroup> */
    public ArrayCollection $userGroups;

    public SettingsBag $settings;

    public function __construct(?User $userEntity = null)
    {
        $this->userEntity = $userEntity;
        $this->email = $userEntity?->getEmail();
        $this->displayName = $userEntity?->getDisplayName();
        $this->accessToBackend = $userEntity?->hasAccessToBackend() ?? true;
        $this->superAdmin = $userEntity?->isSuperAdmin() ?? false;
        $this->userGroups = CollectionHelper::toArrayCollection($userEntity?->getUserGroups());
        $this->settings = $userEntity?->getSettings() ?? new SettingsBag();
    }

    final public function hasEntity(): bool
    {
        return $this->userEntity instanceof User;
    }

    final public function getEntity(): User
    {
        return $this->userEntity;
    }
}
