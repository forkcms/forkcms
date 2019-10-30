<?php

namespace Frontend\Modules\Profiles\Engine;

use Backend\Modules\Profiles\Domain\Profile\Profile;
use Backend\Modules\Profiles\Domain\Profile\Status;
use Backend\Modules\Profiles\Domain\Session\Session;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;

/**
 * Profile authentication functions.
 */
class Authentication
{
    /**
     * The current logged in profile.
     *
     * @var Profile|null
     */
    private static $profile;

    /**
     * Cleanup old session records in the database.
     */
    public static function cleanupOldSessions(): void
    {
        FrontendModel::get('profile.repository.profile_session')->cleanup();
    }

    /**
     * Get the login/profile status for the given e-mail and password.
     *
     * @param string $email Profile email address.
     * @param string $password Profile password.
     *
     * @return string One of the FrontendProfilesAuthentication::LOGIN_* constants.
     */
    public static function getLoginStatus(string $email, string $password): string
    {
        // check password
        if (!FrontendProfilesModel::verifyPassword($email, $password)) {
            return Status::invalid();
        }

        // get the status
        $profile = FrontendModel::get('profile.repository.profile')->findOneByEmail($email);

        if (!$profile instanceof Profile) {
            return (string) Status::inactive();
        }

        return (string) $profile->getStatus();
    }

    public static function getProfile(): ?Profile
    {
        return self::$profile;
    }

    public static function isLoggedIn(): bool
    {
        // profile object exist? (this means the session/cookie checks have
        // already happened in the current request and we cached the profile)
        if (self::$profile instanceof Profile) {
            return true;
        }

        if (FrontendModel::getSession()->get('frontend_profile_logged_in', false) === true) {
            // get session id
            $sessionId = FrontendModel::getSession()->getId();

            // get profile id
            $Session = FrontendModel::get('profile.repository.profile_session')->findOneBySessionId($sessionId);

            if ($Session instanceof Session) {
                $profile = $Session->getProfile();

                $Session->updateDate();
                $profile->registerLogin();
                FrontendModel::get('doctrine.orm.entity_manager')->flush();

                self::$profile = $profile;

                return true;
            }

            // invalid session
            FrontendModel::getSession()->set('frontend_profile_logged_in', false);
        } elseif (FrontendModel::getContainer()->get('fork.cookie')->get('frontend_profile_secret_key', '') !== '') {
            // secret
            $secret = FrontendModel::getContainer()->get('fork.cookie')->get('frontend_profile_secret_key');

            $Session = FrontendModel::get('profile.repository.profile_session')->findOneBySecretKey($secret);

            if ($Session instanceof Session) {
                $profile = $Session->getProfile();

                // get new secret key
                $profileSecret = FrontendProfilesModel::getEncryptedString(
                    FrontendModel::getSession()->getId(),
                    FrontendProfilesModel::getRandomString()
                );

                $Session->updateSecretKey(FrontendModel::getSession()->getId(), $profileSecret);
                $profile->registerLogin();
                FrontendModel::get('doctrine.orm.entity_manager')->flush();

                FrontendModel::getContainer()->get('fork.cookie')->set('frontend_profile_secret_key', $profileSecret);
                FrontendModel::getSession()->set('frontend_profile_logged_in', true);

                self::$profile = $profile;

                return true;
            }

            // invalid cookie
            FrontendModel::getContainer()->get('fork.cookie')->delete('frontend_profile_secret_key');
        }

        // no one is logged in
        return false;
    }

    /**
     * @param int $profileId Login the profile with this id in.
     * @param bool $remember Should we set a cookie for later?
     */
    public static function login(int $profileId, bool $remember = false): void
    {
        $secretKey = null;

        // cleanup old sessions
        self::cleanupOldSessions();

        // set profile_logged_in to true
        FrontendModel::getSession()->set('frontend_profile_logged_in', true);

        // should we remember the user?
        if ($remember) {
            // generate secret key
            $secretKey = FrontendProfilesModel::getEncryptedString(
                FrontendModel::getSession()->getId(),
                FrontendProfilesModel::getRandomString()
            );

            // set cookie
            FrontendModel::getContainer()->get('fork.cookie')->set('frontend_profile_secret_key', $secretKey);
        }

        $SessionRepository = FrontendModel::get('profile.repository.profile_session');
        $profile = FrontendModel::get('profile.repository.profile')->find($profileId);

        // delete all records for this session to prevent duplicate keys (this should never happen)
        $Sessions = $SessionRepository->findBySessionId(FrontendModel::getSession()->getId());
        foreach ($Sessions as $Session) {
            $SessionRepository->remove($Session);
        }

        // insert new session record
        $Session = new Session(
            FrontendModel::getSession()->getId(),
            $profile,
            $secretKey
        );
        $SessionRepository->add($Session);

        // update last login
        $profile->registerLogin();
        FrontendModel::get('doctrine.orm.entity_manager')->flush();

        // load the profile object
        self::$profile = $profile;
    }

    public static function logout(): void
    {
        $SessionRepository = FrontendModel::get('profile.repository.profile_session');
        $Sessions = $SessionRepository->findBySessionId(FrontendModel::getSession()->getId());

        foreach ($Sessions as $Session) {
            $SessionRepository->remove($Session);
        }

        // set is_logged_in to false
        FrontendModel::getSession()->set('frontend_profile_logged_in', false);

        FrontendModel::getContainer()->get('fork.cookie')->delete('frontend_profile_secret_key');
    }

    /**
     * Update profile password and salt.
     *
     * @param int $profileId Profile id for which we are changing the password.
     * @param string $password New password.
     */
    public static function updatePassword(int $profileId, string $password): void
    {
        // encrypt password
        $encryptedPassword = FrontendProfilesModel::encryptPassword($password);

        // update password
        FrontendProfilesModel::update($profileId, ['password' => $encryptedPassword]);
    }
}
