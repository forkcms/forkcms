<?php

namespace Frontend\Modules\Profiles\Actions;

use ForkCMS\Utility\Thumbnails;
use Backend\Modules\Profiles\Domain\Profile\Profile;
use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Model;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

class Settings extends FrontendBaseBlock
{
    /**
     * @var FrontendForm
     */
    private $form;

    /**
     * @var Profile
     */
    private $profile;

    public function execute(): void
    {
        if (!FrontendProfilesAuthentication::isLoggedIn()) {
            throw new InsufficientAuthenticationException('You need to log in to change your settings');
        }

        parent::execute();
        $this->loadTemplate();
        $this->profile = FrontendProfilesAuthentication::getProfile();

        $this->buildForm();
        $this->handleForm();
        $this->parse();
    }

    private function getGenderOptions(): array
    {
        return [
            'male' => \SpoonFilter::ucfirst(FL::getLabel('Male')),
            'female' => \SpoonFilter::ucfirst(FL::getLabel('Female')),
        ];
    }

    private function getBirthDateOptions(): array
    {
        return [
            'days' => range(1, 31),
            'months' => \SpoonLocale::getMonths(LANGUAGE),
            'years' => range(date('Y'), 1900),
        ];
    }

    private function getBirthDate(): array
    {
        $birthDate = $this->profile->getSetting('birth_date');

        return array_combine(
            ['year', 'month', 'day'],
            (empty($birthDate) ? ['', '', ''] : explode('-', $birthDate))
        );
    }

    private function buildForm(): void
    {
        $this->form = new FrontendForm('updateSettings', null, null, 'updateSettingsForm');

        $this->form->addText('display_name', $this->profile->getDisplayName())->makeRequired();
        if (!$this->displayNameCanStillBeChanged()) {
            $this->form->getField('display_name')->setAttribute('disabled', 'disabled');
        }
        $this->form->addText('first_name', $this->profile->getSetting('first_name'));
        $this->form->addText('last_name', $this->profile->getSetting('last_name'));
        $this->form->addText('email', $this->profile->getEmail())->setAttribute('disabled', 'disabled');
        $this->form->addText('city', $this->profile->getSetting('city'));
        $this->form->addDropdown(
            'country',
            Intl::getRegionBundle()->getCountryNames(LANGUAGE),
            $this->profile->getSetting('country')
        )->setDefaultElement('');
        $this->form->addDropdown(
            'gender',
            $this->getGenderOptions(),
            $this->profile->getSetting('gender')
        )->setDefaultElement('');
        ['days' => $days, 'months' => $months, 'years' => $years] = $this->getBirthDateOptions();
        ['year' => $birthYear, 'month' => $birthMonth, 'day' => $birthDay] = $this->getBirthDate();
        $this->form->addDropdown('day', array_combine($days, $days), $birthDay)->setDefaultElement('');
        $this->form->addDropdown('month', $months, $birthMonth)->setDefaultElement('');
        $this->form->addDropdown('year', array_combine($years, $years), (int) $birthYear)->setDefaultElement('');
        $this->form->addImage('avatar');
        $this->form->addTextarea('about', $this->profile->getSetting('about'));
    }

    private function parse(): void
    {
        if ($this->url->getParameter('settingsUpdated') === 'true') {
            $this->template->assign('updateSettingsSuccess', true);
        }

        // assign avatar the current avatar for the preview image
        $this->template->assign('avatar', (string) $this->profile->getSetting('avatar', ''));

        $this->form->parse($this->template);

        // display name changes
        $this->template->assign('maxDisplayNameChanges', FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES);
        $this->template->assign(
            'displayNameChangesLeft',
            FrontendProfilesModel::MAX_DISPLAY_NAME_CHANGES - $this->profile->getSetting('display_name_changes')
        );
    }

    private function getAmountOfDisplayNameChanges(): int
    {
        return (int) FrontendProfilesModel::getSetting($this->profile->getId(), 'display_name_changes');
    }

    private function displayNameCanStillBeChanged(): bool
    {
        return FrontendProfilesModel::displayNameCanStillBeChanged($this->profile);
    }

    private function validateForm(): bool
    {
        $txtDisplayName = $this->form->getField('display_name');
        $ddmDay = $this->form->getField('day');
        $ddmMonth = $this->form->getField('month');
        $ddmYear = $this->form->getField('year');

        if ($this->displayNameCanStillBeChanged()
            && $this->profile->getDisplayName() !== $txtDisplayName->getValue()
            && $txtDisplayName->isFilled(FL::getError('FieldIsRequired'))
            && FrontendProfilesModel::existsDisplayName($txtDisplayName->getValue(), $this->profile->getId())) {
            $txtDisplayName->addError(FL::getError('DisplayNameExists'));
        }

        // birthdate is not required but if one is filled we need all
        if ($ddmMonth->isFilled() || $ddmDay->isFilled() || $ddmYear->isFilled()) {
            // valid birth date?
            if (!checkdate($ddmMonth->getValue(), $ddmDay->getValue(), $ddmYear->getValue())) {
                $ddmYear->addError(FL::getError('DateIsInvalid'));
            }
        }

        // do some basic image checks if an avatar was uploaded
        $this->form->getField('avatar')->isFilled();

        return $this->form->isCorrect();
    }

    private function displayNameWasChanged(): bool
    {
        $txtDisplayName = $this->form->getField('display_name');

        if (!$this->displayNameCanStillBeChanged()
            || $this->profile->getDisplayName() === $txtDisplayName->getValue()) {
            // no change or not allowed to change the display name
            return false;
        }

        FrontendProfilesModel::update(
            $this->profile->getId(),
            [
                'display_name' => $txtDisplayName->getValue(),
                'url' => FrontendProfilesModel::getUrl($txtDisplayName->getValue(), $this->profile->getId()),
            ]
        );

        return true;
    }

    private function handleForm(): void
    {
        if (!$this->form->isSubmitted()) {
            return;
        }

        if (!$this->validateForm()) {
            $this->template->assign('updateSettingsHasFormError', true);

            return;
        }

        $displayNameChanges = $this->getAmountOfDisplayNameChanges();
        if ($this->displayNameWasChanged()) {
            ++$displayNameChanges;
        }

        $this->profile->setSettings(
            [
                'display_name_changes' => $displayNameChanges,
                'first_name' => $this->form->getField('first_name')->getValue(),
                'last_name' => $this->form->getField('last_name')->getValue(),
                'city' => $this->form->getField('city')->getValue(),
                'country' => $this->form->getField('country')->getValue(),
                'gender' => $this->form->getField('gender')->getValue(),
                'birth_date' => $this->getSubmittedBirthDate(),
                'avatar' => $this->getAvatar(),
                'about' => $this->form->getField('about')->getValue(),
            ]
        );
        Model::get('doctrine.orm.entity_manager')->flush();

        $this->redirect(
            FrontendNavigation::getUrlForBlock($this->getModule(), $this->getAction()) . '?settingsUpdated=true'
        );
    }

    private function getAvatar(): ?string
    {
        $currentAvatar = $this->profile->getSetting('avatar');
        if (!$this->form->getField('avatar')->isFilled()) {
            return $currentAvatar;
        }

        $baseAvatarPath = FRONTEND_FILES_PATH . '/Profiles/Avatars/';
        $this->get(Thumbnails::class)->delete($baseAvatarPath, $currentAvatar);

        $newAvatar = $this->profile->getUrl() . '.' . $this->form->getField('avatar')->getExtension();

        $this->form->getField('avatar')->generateThumbnails($baseAvatarPath, $newAvatar);

        return $newAvatar;
    }

    private function getSubmittedBirthDate(): ?string
    {
        if (!$this->form->getField('year')->isFilled()) {
            return null;
        }

        return sprintf(
            '%1$s-%2$s-%3$s',
            $this->form->getField('year')->getValue(),
            str_pad($this->form->getField('month')->getValue(), 2, '0', STR_PAD_LEFT),
            str_pad($this->form->getField('day')->getValue(), 2, '0', STR_PAD_LEFT)
        );
    }
}
