<?php

namespace Frontend\Modules\Profiles\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Activate extends FrontendBaseBlock
{
    public function execute(): void
    {
        $this->loadTemplate();
        $profileId = $this->getProfileId();
        $this->activateProfile($profileId);

        FrontendProfilesAuthentication::login($profileId);

        $this->template->assign('activationSuccess', true);
    }

    private function activateProfile(int $profileId): void
    {
        FrontendProfilesModel::update($profileId, ['status' => 'active']);
        FrontendProfilesModel::deleteSetting($profileId, 'activation_key');
    }

    private function getProfileId(): int
    {
        $profileId = FrontendProfilesModel::getIdBySetting('activation_key', $this->getActivationKey());

        if ($profileId === 0) {
            throw new NotFoundHttpException();
        }

        return $profileId;
    }

    private function getActivationKey(): string
    {
        $activationKey = $this->url->getParameter(0);

        if ($activationKey === null) {
            throw new NotFoundHttpException();
        }

        return $activationKey;
    }
}
