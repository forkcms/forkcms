<?php

namespace Backend\Modules\Profiles\Domain\ProfileGroupRight;

use Backend\Modules\Profiles\Domain\Profile\Profile;
use Backend\Modules\Profiles\Domain\ProfileGroup\ProfileGroup;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileGroupRightDataTransferObject
{
    /**
     * @var ProfileGroupRight
     */
    protected $profileGroupRightEntity;

    /**
     * @var int|null
     */
    public $id;

    /**
     * @var Profile
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $profile;

    /**
     * @var ProfileGroup
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $group;

    /**
     * @var DateTime|null
     *
     * @Assert\DateTime(message="err.DateIsInvalid")
     */
    public $startsOn;

    /**
     * @var DateTime|null
     *
     * @Assert\DateTime(message="err.DateIsInvalid")
     * @Assert\Expression(
     *     "this.isDateRangeValid()",
     *     message="err.DateRangeIsInvalid"
     * )
     */
    public $expiresOn;

    public function __construct(ProfileGroupRight $profileGroupRight = null)
    {
        if (!($profileGroupRight instanceof ProfileGroupRight)) {
            return;
        }

        $this->profileGroupRightEntity = $profileGroupRight;

        $this->id = $profileGroupRight->getId();
        $this->profile = $profileGroupRight->getProfile();
        $this->group = $profileGroupRight->getGroup();
        $this->startsOn = $profileGroupRight->getStartDate();
        $this->expiresOn = $profileGroupRight->getExpiryDate();
    }

    public function getProfileGroupRightEntity(): ProfileGroupRight
    {
        return $this->profileGroupRightEntity;
    }

    private function isDateRangeValid(): bool
    {
        if ($this->startsOn === null || $this->expiresOn === null) {
            return true;
        }

        return $this->startsOn < $this->expiresOn;
    }
}
