<?php

namespace Backend\Modules\Users\Installer;

use Backend\Core\Engine\Authentication;
use Common\Core\Model;
use Symfony\Component\Filesystem\Filesystem;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Groups\Engine\Model as BackendGroupsModel;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;

/**
 * Installer for the users module
 */
class Installer extends ModuleInstaller
{
    public function install(): void
    {
        $this->addModule('Users');
        $this->importSQL(__DIR__ . '/Data/install.sql');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureSettings();
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->loadGodUser();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $this->setNavigation(
            $navigationSettingsId,
            'Users',
            'users/index',
            [
                'users/add',
                'users/edit',
            ],
            4
        );
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'UndoDelete');
    }

    private function configureSettings(): void
    {
        $this->setSetting($this->getModule(), 'date_formats', ['j/n/Y', 'd/m/Y', 'j F Y', 'F j, Y']);
        $this->setSetting($this->getModule(), 'default_group', 1);
        $this->setSetting($this->getModule(), 'time_formats', ['H:i', 'H:i:s', 'g:i a', 'g:i A']);
    }

    public function getPasswordStrength(): string
    {
        $password = $this->getVariable('password');
        $score = 0;
        $uniqueChars = [];

        // less then 4 chars is just a weak password
        if (mb_strlen($password) <= 4) {
            return 'weak';
        }

        // loop chars and add unique chars
        $passwordChars = str_split($password);
        foreach ($passwordChars as $char) {
            $uniqueChars[$char] = $char;
        }

        // less then 3 unique chars is just weak
        if (count($uniqueChars) < 3) {
            return 'weak';
        }

        // more then 6 chars is good
        if (mb_strlen($password) >= 6) {
            ++$score;
        }

        // more then 8 is better
        if (mb_strlen($password) >= 8) {
            ++$score;
        }

        // @todo
        // upper and lowercase?
        if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) {
            $score += 2;
        }

        // number?
        if (preg_match('/\d+/', $password)) {
            ++$score;
        }

        // special char?
        if (preg_match('/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/', $password)) {
            ++$score;
        }

        // strong password
        if ($score >= 4) {
            return 'strong';
        }

        // average
        if ($score >= 2) {
            return 'average';
        }

        // fallback
        return 'weak';
    }

    private function hasExistingGodUser(): bool
    {
        // @todo: Replace by UserRepository method when it exists.
        return (bool) $this->getDatabase()->getVar(
            'SELECT 1
             FROM users
             WHERE is_god = ? AND deleted = ? AND active = ?
             LIMIT 1',
            [true, false, true]
        );
    }

    /**
     * @todo: Move this code to a DataFixture and use User entity when it exists.
     */
    private function loadGodUser(): void
    {
        if (!$this->hasExistingGodUser()) {
            $this->loadGodUserAvatar();

            // build settings
            $settings = [];
            $settings['nickname'] = 'Fork CMS';
            $settings['name'] = 'Fork';
            $settings['preferred_editor'] = Model::getContainer()->getParameter('fork.form.default_preferred_editor');
            $settings['surname'] = 'CMS';
            $settings['interface_language'] = $this->getVariable('default_interface_language');
            $settings['date_format'] = 'j F Y';
            $settings['time_format'] = 'H:i';
            $settings['datetime_format'] = $settings['date_format'] . ' ' . $settings['time_format'];
            $settings['number_format'] = 'dot_nothing';
            $settings['password_key'] = uniqid('', true);
            $settings['password_strength'] = $this->getPasswordStrength();
            $settings['current_password_change'] = time();
            $settings['avatar'] = 'god.jpg';
            $possibleCSVSplitCharacters = BackendUsersModel::getCSVSplitCharacters();
            $settings['csv_split_character'] = reset($possibleCSVSplitCharacters);
            $possibleCSVLineEndings = BackendUsersModel::getCSVLineEndings();
            $settings['csv_line_ending'] = reset($possibleCSVLineEndings);

            // build user
            $user = [];
            $user['email'] = $this->getVariable('email');
            $user['password'] = Authentication::encryptPassword($this->getVariable('password'));
            $user['active'] = true;
            $user['deleted'] = false;
            $user['is_god'] = true;

            // insert user
            $user['id'] = BackendUsersModel::insert($user, $settings);

            // build groups
            $groups = [$this->getSetting('Users', 'default_group')];

            // insert group
            BackendGroupsModel::insertMultipleGroups($user['id'], $groups);
        }
    }

    private function loadGodUserAvatar(): void
    {
        // secret files
        $avatar124x124 = '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';
        $avatar64x64 = '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';
        $avatar32x32 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAYEBAQFBAYFBQYJBgUGCQsIBgYICwwKCgsKCgwQDAwMDAwMEAwODxAPDgwTExQUExMcGxsbHCAgICAgICAgICD/2wBDAQcHBw0MDRgQEBgaFREVGiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICD/wAARCAAgACADAREAAhEBAxEB/8QAFwABAQEBAAAAAAAAAAAAAAAABwYIBf/EACsQAAICAQIFBAEEAwAAAAAAAAECAwQFBhEABxIhMRMiI0EUMkJRYjNxsf/EABkBAAMBAQEAAAAAAAAAAAAAAAMEBQIGAP/EACcRAAEDBAIBAgcAAAAAAAAAAAEAAgMEERIhMUEFIlETMkNhYqGx/9oADAMBAAIRAxEAPwBd1Bm1xmDzV+Svt+BUllsM/T6khrwGWIORsQC3j7HHNwRZWB1/U05yxbe5081Ll6S42pr8Mkm/x15mhiUH9qxpsoA4viBgFraS2S1Fye5jZXWOlMdmZ7rS5DDQvTyVLZfnn7BbMkh9wYxDwPvfhCqFiiN91eQx5OxkJQd54W96vI3tA3I6Ytu/b+3HqSLM5FYlJGlj7W/OPm1ee9pjJl8dFKHr28U9dRMVYbFXZ19TwPrbhmOnYPVz91oZHQU7hOXmTyVCC4sRaNmPXGp6JNlOzfqBA8cDkr2tdiqcPiHPYHXtfpNPK/HZ3GtJiNKYWrSW2yPYzF+zLO3WnYdEaxhSR32UrtvxhlQ2XraHW0PwBzdO2D6sLh5IL99rFwyrHJb+wG3YHuz+Bue7H/g4ZpiP2pkiMuYNXEZ2pm1BE+o5HEWIlkhTrgesAy2I32L+mxU9R+h54lxEg/imC/E3HKOMflreJ3qWBFHLY91doyWg3bb1CjDfdQd+FpI966XYRS5NBPe0kcrxaTHZC8bnVEfhrtGpQI0fySfqALbp+76/3wSL0hSfLG9gqGjmFs5RKNf5UqL+Shk3d36wAqEnzsfP3xuNxCiH2WcNYc58jdpCpg3kgntwSQ5O6B0hhZ2M0MCHqIDEDqlJ63/qO3FaOmtyg5KpwFFtQ6arXHhia56MT+kjArJ6a+mZFU90l9vu2Pu+u/lOuhsch2rfiq76b+BwqPR0WZ01pzJay3rtiJJoqn4lpmWKaMSrBMP4j7vv1n+O443Rw32UPzE+8Grsya+0lj9RVYI6k+IztpDUmh6W/wAtdDtH0AyKrk+xl7HurKWB4bkomO+XSiNkIX//2Q==';

        $filesystem = new Filesystem();

        // store files
        $filesystem->dumpFile(
            PATH_WWW . '/src/Frontend/Files/Users/avatars/source/god.jpg',
            base64_decode($avatar124x124)
        );
        $filesystem->dumpFile(
            PATH_WWW . '/src/Frontend/Files/Users/avatars/128x128/god.jpg',
            base64_decode($avatar124x124)
        );
        $filesystem->dumpFile(
            PATH_WWW . '/src/Frontend/Files/Users/avatars/64x64/god.jpg',
            base64_decode($avatar64x64)
        );
        $filesystem->dumpFile(
            PATH_WWW . '/src/Frontend/Files/Users/avatars/32x32/god.jpg',
            base64_decode($avatar32x32)
        );
    }
}
