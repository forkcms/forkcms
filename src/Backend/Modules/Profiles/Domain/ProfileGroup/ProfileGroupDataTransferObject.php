<?php

namespace Backend\Modules\Profiles\Domain\ProfileGroup;

use Symfony\Component\Validator\Constraints as Assert;

class ProfileGroupDataTransferObject
{
    /**
     * @var ProfileGroup
     */
    protected $profileGroupEntity;

    /**
     * @var int|null
     */
    public $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $name;

    public function __construct(ProfileGroup $profileGroup = null)
    {
        if (!($profileGroup instanceof ProfileGroup)) {
            return;
        }

        $this->profileGroupEntity = $profileGroup;

        $this->id = $profileGroup->getId();
        $this->name = $profileGroup->getName();
    }

    public function getProfileGroupEntity(): ProfileGroup
    {
        return $this->profileGroupEntity;
    }
}
