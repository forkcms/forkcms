<?php

namespace Backend\Modules\Profiles\Domain\Profile;

use Symfony\Component\Validator\Constraints as Assert;

class ProfileDataTransferObject
{
    /**
     * @var Profile
     */
    protected $profileEntity;

    /**
     * @var int|null
     */
    public $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $email;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $password;

    /**
     * @var Status
     */
    public $status;

    /**
     * @var string
     */
    public $displayName;

    /**
     * @var string
     */
    public $url;

    public function __construct(Profile $profile = null)
    {
        if (!($profile instanceof Profile)) {
            return;
        }

        $this->profileEntity = $profile;

        $this->id = $profile->getId();
        $this->email = $profile->getEmail();
        $this->password = $profile->getPassword();
        $this->status = $profile->getStatus();
        $this->displayName = $profile->getDisplayName();
        $this->url = $profile->getUrl();
    }

    public function getProfileEntity(): Profile
    {
        return $this->profileEntity;
    }
}
