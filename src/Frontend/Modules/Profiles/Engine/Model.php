<?php

namespace Frontend\Modules\Profiles\Engine;

use Backend\Modules\Profiles\Domain\Profile\Profile;
use Backend\Modules\Profiles\Domain\Profile\Status;
use Backend\Modules\Profiles\Domain\ProfileSetting\ProfileSetting;
use Common\Uri as CommonUri;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

/**
 * In this file we store all generic functions that we will be using with profiles.
 */
class Model
{
    const MAX_DISPLAY_NAME_CHANGES = 2;

    /**
     * Avatars cache
     *
     * @var array
     */
    private static $avatars = [];

    public static function deleteSetting(int $profileId, string $name): void
    {
        $profileSettingRepository = FrontendModel::get('profile.repository.profile_setting');

        $profile = FrontendModel::get('profile.repository.profile')->find($profileId);
        $profileSetting = $profileSettingRepository->findOneBy(
            [
                'profile' => $profile,
                'name' => $name,
            ]
        );

        $profileSettingRepository->remove($profileSetting);
    }

    public static function existsByEmail(string $email, int $excludedId = 0): bool
    {
        return FrontendModel::get('profile.repository.profile')->existsByEmail($email, $excludedId);
    }

    public static function existsDisplayName(string $displayName, int $excludedId = 0): bool
    {
        return FrontendModel::get('profile.repository.profile')->existsByDisplayName($displayName, $excludedId);
    }

    public static function get(int $profileId): Profile
    {
        return FrontendModel::get('profile.repository.profile')->find($profileId);
    }

    /**
     * @param int $id The id for the profile we want to get the avatar from.
     * @param string $email The email from the user we can use for gravatar.
     * @param string $size The resolution you want to use. Default: 240x240 pixels.
     *
     * @return string $avatar The absolute path to the avatar.
     */
    public static function getAvatar(int $id, string $email = null, string $size = '240x240'): string
    {
        // return avatar from cache
        if (isset(self::$avatars[$id])) {
            return self::$avatars[$id];
        }

        // define avatar path
        $avatarPath = FRONTEND_FILES_URL . '/Profiles/Avatars/' . $size . '/';

        // get user
        $user = self::get($id);

        // if no email is given
        if (empty($email)) {
            // redefine email
            $email = $user->getEmail();
        }

        // define avatar
        $avatar = $user->getSetting('avatar');

        // no custom avatar defined, get gravatar if allowed
        if (empty($avatar) && FrontendModel::get('fork.settings')->get('Profiles', 'allow_gravatar', true)) {
            // define hash
            $hash = md5(mb_strtolower(trim('d' . $email)));

            // define avatar url
            $avatar = 'https://www.gravatar.com/avatar/' . $hash;

            // when email not exists, it has to show our custom no-avatar image
            $avatar .= '?d=' . rawurlencode(SITE_URL . $avatarPath) . 'no-avatar.gif';
        } elseif (empty($avatar)) {
            // define avatar as not found
            $avatar = SITE_URL . $avatarPath . 'no-avatar.gif';
        } else {
            // define custom avatar path
            $avatar = $avatarPath . $avatar;
        }

        // set avatar in cache
        self::$avatars[$id] = $avatar;

        // return avatar image path
        return $avatar;
    }

    /**
     * Encrypt the password with PHP password_hash function.
     *
     * @param string $password
     *
     * @return string
     */
    public static function encryptPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify the password with PHP password_verify function.
     *
     * @param string $email
     * @param string $password
     *
     * @return bool
     */
    public static function verifyPassword(string $email, string $password): bool
    {
        $encryptedPassword = self::getEncryptedPassword($email);

        return password_verify($password, $encryptedPassword);
    }

    /**
     * @param string $string
     * @param string $salt
     *
     * @return string
     */
    public static function getEncryptedString(string $string, string $salt): string
    {
        return md5(sha1(md5($string)) . sha1(md5($salt)));
    }

    public static function getIdByEmail(string $email): int
    {
        $profile = FrontendModel::get('profile.repository.profile')->findOneByEmail($email);

        return $profile->getId();
    }

    /**
     * @param string $name Setting name.
     * @param mixed $value Value of the setting.
     *
     * @return int
     */
    public static function getIdBySetting(string $name, $value): ?int
    {
        $profileSetting = FrontendModel::get('profile.repository.profile_setting')->findOneBy(
            [
                'name' => $name,
                'value' => $value,
            ]
        );

        if (!$profileSetting instanceof ProfileSetting) {
            return null;
        }

        return $profileSetting->getProfile()->getId();
    }

    /**
     * @param int $length Length of random string.
     * @param bool $numeric Use numeric characters.
     * @param bool $lowercase Use alphanumeric lowercase characters.
     * @param bool $uppercase Use alphanumeric uppercase characters.
     * @param bool $special Use special characters.
     *
     * @return string
     */
    public static function getRandomString(
        int $length = 15,
        bool $numeric = true,
        bool $lowercase = true,
        bool $uppercase = true,
        bool $special = true
    ): string {
        // init
        $characters = '';
        $string = '';
        $charset = FrontendModel::getContainer()->getParameter('kernel.charset');

        // possible characters
        if ($numeric) {
            $characters .= '1234567890';
        }
        if ($lowercase) {
            $characters .= 'abcdefghijklmnopqrstuvwxyz';
        }
        if ($uppercase) {
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if ($special) {
            $characters .= '-_.:;,?!@#&=)([]{}*+%$';
        }

        // get random characters
        for ($i = 0; $i < $length; ++$i) {
            // random index
            $index = mt_rand(0, mb_strlen($characters) - 1);

            // add character to salt
            $string .= mb_substr($characters, $index, 1, $charset);
        }

        return $string;
    }

    public static function getSetting(int $id, string $name): ?string
    {
        $profile = FrontendModel::get('profile.repository.profile')->find($id);
        $setting = FrontendModel::get('profile.repository.profile_setting')->findOneBy(
            [
                'profile' => $profile,
                'name' => $name,
            ]
        );

        if (!$setting instanceof ProfileSetting) {
            return null;
        }

        return $setting->getValue();
    }

    public static function getSettings(int $profileId): array
    {
        $profile = FrontendModel::get('profile.repository.profile')->find($profileId);
        $profileSettings = $profile->getSettings();

        $settings = [];
        foreach ($profileSettings as $profileSetting) {
            $settings[$profileSetting->getName()] = $profileSetting->getValue();
        }

        return $settings;
    }

    /**
     * Retrieve a unique URL for a profile based on the display name.
     *
     * @param string $displayName The display name to base on.
     * @param int $excludedId The id of the profile to ignore.
     *
     * @return string
     */
    public static function getUrl(string $displayName, int $excludedId = null): string
    {
        // decode special chars
        $displayName = \SpoonFilter::htmlspecialcharsDecode($displayName);

        // urlise
        $url = CommonUri::getUrl($displayName);

        return FrontendModel::get('profile.repository.profile')->getUrl(
            $url,
            $excludedId
        );
    }

    public static function insert(array $profile): int
    {
        $profileEntity = new Profile(
            $profile['email'],
            $profile['password'],
            Status::fromString($profile['status']),
            $profile['display_name'],
            $profile['url']
        );

        FrontendModel::get('profile.repository.profile')->add($profileEntity);

        return $profileEntity->getId();
    }

    /**
     * Parse the general profiles info into the template.
     */
    public static function parse(): void
    {
        // get the template
        $tpl = FrontendModel::getContainer()->get('templating');

        // logged in
        if (FrontendProfilesAuthentication::isLoggedIn()) {
            // get profile
            $profile = FrontendProfilesAuthentication::getProfile();

            // display name set?
            if ($profile->getDisplayName() != '') {
                $tpl->assign('profileDisplayName', $profile->getDisplayName());
            } else {
                // no display name -> use email
                $tpl->assign('profileDisplayName', $profile->getEmail());
            }

            // show logged in
            $tpl->assign('isLoggedIn', true);
        }

        // ignore these urls in the query string
        $ignoreUrls = [
            FrontendNavigation::getUrlForBlock('Profiles', 'Login'),
            FrontendNavigation::getUrlForBlock('Profiles', 'Register'),
            FrontendNavigation::getUrlForBlock('Profiles', 'ForgotPassword'),
        ];

        // query string
        $queryString = FrontendModel::getRequest()->query->has('queryString')
            ? SITE_URL . '/' . urldecode(FrontendModel::getRequest()->query->get('queryString'))
            : SITE_URL . FrontendModel::get('url')->getQueryString();

        // check all ignore urls
        foreach ($ignoreUrls as $url) {
            // query string contains a boeboe url
            if (mb_stripos($queryString, $url) !== false) {
                $queryString = '';
                break;
            }
        }

        // no need to add this if its empty
        $queryString = ($queryString !== '') ? '?queryString=' . rawurlencode($queryString) : '';

        // useful urls
        $tpl->assign('loginUrl', FrontendNavigation::getUrlForBlock('Profiles', 'Login') . $queryString);
        $tpl->assign('registerUrl', FrontendNavigation::getUrlForBlock('Profiles', 'Register'));
        $tpl->assign('forgotPasswordUrl', FrontendNavigation::getUrlForBlock('Profiles', 'ForgotPassword'));
    }

    /**
     * Insert or update a single profile setting.
     *
     * @param int $id Profile id.
     * @param string $name Setting name.
     * @param mixed $value New setting value.
     */
    public static function setSetting(int $id, string $name, $value): void
    {
        $profileSettingRepository = FrontendModel::get('profile.repository.profile_setting');

        $profile = FrontendModel::get('profile.repository.profile')->find($id);

        $existingSetting = $profileSettingRepository->findOneBy(
            [
                'profile' => $profile,
                'name' => $name,
            ]
        );

        if ($existingSetting instanceof ProfileSetting) {
            $existingSetting->update($value);

            FrontendModel::get('doctrine.orm.default_entity_manager')->flush();

            return;
        }

        $setting = new ProfileSetting($profile, $name, $value);
        $profile->addSetting($setting);
        $profileSettingRepository->add($setting);
    }

    /**
     * Insert or update multiple profile settings.
     *
     * @param int $id Profile id.
     * @param array $values Settings in key=>value form.
     */
    public static function setSettings(int $id, array $values): void
    {
        foreach ($values as $name => $value) {
            self::setSetting($id, $name, $value);
        }
    }

    /**
     * @param int $id The profile id.
     * @param array $values The values to update.
     *
     * @return int
     */
    public static function update(int $id, array $values): int
    {
        $profile = FrontendModel::get('profile.repository.profile')->find($id);

        if (!$profile instanceof Profile) {
            return $id;
        }

        $email = $profile->getEmail();
        if (array_key_exists('email', $values)) {
            $email = $values['email'];
        }
        $password = $profile->getPassword();
        if (array_key_exists('password', $values)) {
            $password = $values['password'];
        }
        $status = $profile->getStatus();
        if (array_key_exists('status', $values)) {
            $status = Status::fromString($values['status']);
        }
        $displayName = $profile->getDisplayName();
        if (array_key_exists('display_name', $values)) {
            $displayName = $values['display_name'];
        }
        $url = $profile->getUrl();
        if (array_key_exists('url', $values)) {
            $url = $values['url'];
        }

        $profile->update(
            $email,
            $password,
            $status,
            $displayName,
            $url
        );

        FrontendModel::get('doctrine.orm.entity_manager')->flush();

        return $id;
    }

    /**
     * Get encrypted password for an email.
     *
     * @param string $email
     *
     * @return null|string
     */
    public static function getEncryptedPassword(string $email): ?string
    {
        $profile = FrontendModel::get('profile.repository.profile')->findOneByEmail($email);

        if (!$profile instanceof Profile) {
            return null;
        }

        return $profile->getPassword();
    }
}
