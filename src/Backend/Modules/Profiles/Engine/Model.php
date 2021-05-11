<?php

namespace Backend\Modules\Profiles\Engine;

use Backend\Modules\Profiles\Domain\Group\GroupRepository;
use Backend\Modules\Profiles\Domain\GroupRight\GroupRightRepository;
use Backend\Modules\Profiles\Domain\Profile\Profile;
use Backend\Modules\Profiles\Domain\Profile\ProfileRepository;
use Backend\Modules\Profiles\Domain\Profile\Status;
use Backend\Modules\Profiles\Domain\Group\Group;
use Backend\Modules\Profiles\Domain\GroupRight\GroupRight;
use Backend\Modules\Profiles\Domain\Session\SessionRepository;
use Backend\Modules\Profiles\Domain\Setting\Setting;
use Backend\Modules\Profiles\Domain\Setting\SettingRepository;
use Common\Mailer\Message;
use Common\Uri as CommonUri;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Exception as BackendException;
use DateTime;

/**
 * In this file we store all generic functions that we will be using in the profiles module.
 */
class Model
{
    /**
     * @var array The possible status
     */
    public const POSSIBLE_STATUS = [
        'active',
        'inactive',
        'deleted',
        'blocked',
    ];

    /**
     * Cache avatars
     *
     * @param string
     */
    protected static $avatars;

    /**
     * Browse groups for datagrid.
     * The hidden field is added to display the expired group links as greyed out
     *
     * @var string
     */
    const QUERY_DATAGRID_BROWSE_PROFILE_GROUPS =
        'SELECT gr.id, g.name AS group_name, IF(gr.expiresOn IS NULL, 0, UNIX_TIMESTAMP(gr.expiresOn)) AS expires_on,
          IF(gr.expiresOn IS NOT NULL AND gr.expiresOn <= NOW(), 1, 0) AS hidden
         FROM ProfilesGroup AS g
         INNER JOIN ProfilesGroupRight AS gr ON gr.group_id = g.id
         WHERE gr.profile_id = ?';

    /**
     * Delete the given profiles.
     *
     * @param mixed $ids One ID, or an array of IDs.
     */
    public static function delete($ids): void
    {
        // redefine
        $ids = (array) $ids;

        $profileRepository = BackendModel::get(ProfileRepository::class);

        // delete profiles
        foreach ($ids as $id) {
            $profile = $profileRepository->find($id);

            self::deleteSession($id);

            $profile->delete();
        }

        BackendModel::get('doctrine.orm.entity_manager')->flush();
    }

    public static function deleteGroup(int $groupId): void
    {
        $group = BackendModel::get(GroupRepository::class)->find($groupId);

        BackendModel::get(GroupRepository::class)->remove($group);
    }

    /**
     * Delete a membership of a profile in a group.
     *
     * @param int $membershipId Id of the membership.
     */
    public static function deleteProfileGroup(int $membershipId): void
    {
        $groupRight = BackendModel::get(GroupRightRepository::class)->find($membershipId);

        BackendModel::get(GroupRightRepository::class)->remove($groupRight);
    }

    public static function deleteSession(int $id): void
    {
        $profile = BackendModel::get(ProfileRepository::class)->find($id);

        $SessionRepository = BackendModel::get(SessionRepository::class);

        $sessions = $SessionRepository->findByProfile($profile);
        foreach ($sessions as $session) {
            $SessionRepository->remove($session);
        }
    }

    public static function exists(int $profileId): bool
    {
        return BackendModel::get(ProfileRepository::class)->find($profileId) instanceof Profile;
    }

    public static function existsByEmail(string $email, int $excludedProfileId = 0): bool
    {
        return BackendModel::get(ProfileRepository::class)->existsByEmail($email, $excludedProfileId);
    }

    public static function existsDisplayName(string $displayName, int $excludedProfileId = 0): bool
    {
        return BackendModel::get(ProfileRepository::class)->existsByDisplayName($displayName, $excludedProfileId);
    }

    public static function existsGroup(int $groupId): bool
    {
        return BackendModel::get(GroupRepository::class)->find($groupId) instanceof Group;
    }

    public static function existsGroupName(string $groupName, int $excludedGroupId = 0): bool
    {
        return BackendModel::get(GroupRepository::class)->existsByName($groupName, $excludedGroupId);
    }

    public static function existsProfileGroup(int $membershipId): bool
    {
        $groupRight = BackendModel::get(GroupRightRepository::class)->find($membershipId);

        return $groupRight instanceof GroupRight;
    }

    public static function get(int $profileId): array
    {
        $profile = BackendModel::get(ProfileRepository::class)->find($profileId);

        if (!$profile instanceof Profile) {
            return [];
        }

        return $profile->toArray();
    }

    /**
     * Get avatar
     *
     * @param int $profileId The id for the profile we want to get the avatar from.
     * @param string $email The email from the user we can use for gravatar.
     *
     * @return string $avatar            The absolute path to the avatar.
     */
    public static function getAvatar(int $profileId, string $email = null): string
    {
        // return avatar from cache
        if (isset(self::$avatars[$profileId])) {
            return self::$avatars[$profileId];
        }

        // define avatar path
        $avatarPath = FRONTEND_FILES_URL . '/Profiles/Avatars/32x32/';

        // get avatar for profile
        $avatar = self::getSetting($profileId, 'avatar');

        // if no email is given
        if ($email === null) {
            // get user
            $user = self::get($profileId);

            // redefine email
            $email = $user['email'];
        }

        // no custom avatar defined, get gravatar if allowed
        if (empty($avatar) && BackendModel::get('fork.settings')->get('Profiles', 'allow_gravatar', true)) {
            // define hash
            $hash = md5(mb_strtolower(trim('d' . $email)));

            // define avatar url
            $avatar = 'https://www.gravatar.com/avatar/' . $hash;

            // when email not exists, it has to show our custom no-avatar image
            $avatar .= '?d=' . SITE_URL . $avatarPath . 'no-avatar.gif';
        } elseif (empty($avatar)) {
            // define avatar as not found
            $avatar = SITE_URL . $avatarPath . 'no-avatar.gif';
        } else {
            // define custom avatar path
            $avatar = $avatarPath . $avatar;
        }

        // set avatar in cache
        self::$avatars[$profileId] = $avatar;

        // return avatar image path
        return $avatar;
    }

    public static function getByEmail(string $email): array
    {
        $profile = BackendModel::get(ProfileRepository::class)->findOneByEmail($email);

        if (!$profile instanceof Profile) {
            return [];
        }

        return $profile->toArray();
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
     * Encrypt a string with a salt.
     *
     * @param string $string String to encrypt.
     * @param string $salt Salt to saltivy the string with.
     *
     * @return string
     */
    public static function getEncryptedString(string $string, string $salt): string
    {
        return md5(sha1(md5($string)) . sha1(md5($salt)));
    }

    public static function getGroup(int $groupId): array
    {
        $group = BackendModel::get(GroupRepository::class)->find($groupId);

        if (!$group instanceof Group) {
            return [];
        }

        return $group->toArray();
    }

    /**
     * Get the list of all groups as array($groupId => $groupName).
     *
     * @return array
     */
    public static function getGroups(): array
    {
        $groupsArray = [];
        $groups = BackendModel::get(GroupRepository::class)->findBy([], ['name' => 'ASC']);
        foreach ($groups as $group) {
            $groupsArray[$group->getId()] = $group->getName();
        }

        return $groupsArray;
    }

    /**
     * Get profile groups for dropdown not yet linked to a profile
     *
     * @param int $profileId Profile id.
     * @param int|null $includeId Group id to always include.
     *
     * @return array
     */
    public static function getGroupsForDropDown(int $profileId, int $includeId = null): array
    {
        $profile = BackendModel::get(ProfileRepository::class)->find($profileId);

        $linkedGroups = BackendModel::get(GroupRightRepository::class)
            ->findLinkedToProfile($profile, $includeId);
        $excludeGroupIds = array_map(
            function (GroupRight $groupRight) {
                return $groupRight->getGroup()->getId();
            },
            $linkedGroups
        );

        $groups = BackendModel::get(GroupRepository::class)->findWithExcludedIds($excludeGroupIds);

        $groupsArray = [];
        foreach ($groups as $group) {
            $groupsArray[$group->getId()] = $group->getName();
        }

        return $groupsArray;
    }

    /**
     * Get information about a profile group where a user is member of.
     *
     * @param int $membershipId
     *
     * @return array
     */
    public static function getProfileGroup(int $membershipId): array
    {
        $groupRight = BackendModel::get(GroupRightRepository::class)->find($membershipId);

        if (!$groupRight instanceof GroupRight) {
            return [];
        }

        return $groupRight->toArray();
    }

    /**
     * Get the groups where a profile is member of.
     *
     * @param int $profileId The profile id to get the groups for.
     *
     * @return array
     */
    public static function getProfileGroups(int $profileId): array
    {
        $profile = BackendModel::get(ProfileRepository::class)->find($profileId);
        $groupRights = BackendModel::get(GroupRightRepository::class)->findByProfile($profile);

        return array_map(
            static function (GroupRight $groupRight) {
                return [
                    'id' => $groupRight->getId(),
                    'group_id' => $groupRight->getGroup()->getId(),
                    'group_name' => $groupRight->getGroup()->getName(),
                    'expires_on' => $groupRight->getExpiryDate()->getTimestamp(),
                ];
            },
            $groupRights
        );
    }

    /**
     * Generate a random string.
     *
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
        $charset = BackendModel::getContainer()->getParameter('kernel.charset');

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
            $index = mt_rand(0, mb_strlen($characters));

            // add character to salt
            $string .= mb_substr($characters, $index, 1, $charset);
        }

        // cough up
        return $string;
    }

    public static function getSetting(int $profileId, string $name): ?string
    {
        $profile = BackendModel::get(ProfileRepository::class)->find($profileId);
        $setting = BackendModel::get(SettingRepository::class)->findOneBy(
            [
                'profile' => $profile,
                'name' => $name,
            ]
        );

        if (!$setting instanceof Setting) {
            return null;
        }

        return $setting->getValue();
    }

    public static function getStatusForDropDown(): array
    {
        $labels = static::POSSIBLE_STATUS;

        // loop and build labels
        foreach ($labels as &$row) {
            $row = \SpoonFilter::ucfirst(BL::getLabel(\SpoonFilter::ucfirst($row)));
        }

        // build array
        return array_combine(static::POSSIBLE_STATUS, $labels);
    }

    /**
     * Retrieve a unique URL for a profile based on the display name.
     *
     * @param string $displayName The display name to base on.
     * @param int|null $excludedProfileId
     *
     * @return string
     */
    public static function getUrl(string $displayName, int $excludedProfileId = null): string
    {
        // decode specialchars
        $displayName = \SpoonFilter::htmlspecialcharsDecode((string) $displayName);

        // urlise
        $url = CommonUri::getUrl($displayName);

        return BackendModel::get(ProfileRepository::class)->getUrl(
            $url,
            $excludedProfileId
        );
    }

    /**
     * Get the HTML for a user to use in a datagrid
     *
     * @param int $id The Id of the user.
     *
     * @return string
     */
    public static function getUser(int $id): string
    {
        // create user instance
        $user = self::get($id);

        // no user found, stop here
        if (empty($user)) {
            return '';
        }

        // get settings
        $nickname = $user['display_name'];
        $allowed = BackendAuthentication::isAllowedAction('Edit', 'Profiles');

        // get avatar
        $avatar = self::getAvatar($id, $user['email']);

        // build html
        $html = '<div class="dataGridAvatar">' . "\n";
        $html .= '  <div class="avatar av24">' . "\n";
        if ($allowed) {
            $html .= '      <a href="' . BackendModel::createUrlForAction(
                'Edit',
                'Profiles'
            ) . '&amp;id=' . $id . '">' . "\n";
        }
        $html .= '          <img src="' . $avatar . '" width="24" height="24" alt="' . $nickname . '" />' . "\n";
        if ($allowed) {
            $html .= '      </a>' . "\n";
        }
        $html .= '  </div>';
        $html .= '  <p><a href="' . BackendModel::createUrlForAction(
            'Edit',
            'Profiles'
        ) . '&amp;id=' . $id . '">' . $nickname . '</a></p>' . "\n";
        $html .= '</div>';

        return $html;
    }

    /**
     * Import CSV data
     *
     * @deprecated remove this in Fork 6, use Backend\Modules\Profiles\Engine::importFromArray
     *
     * @param array $data The array from the .csv file
     * @param int|null $groupId $groupId Adding these profiles to a group
     * @param bool $overwriteExisting $overwriteExisting
     *
     * @throws BackendException
     *
     * @return array array('count' => array('exists' => 0, 'inserted' => 0));
     *
     * @internal param $bool [optional] $overwriteExisting If set to true, this will overwrite existing profiles
     */
    public static function importCsv(array $data, int $groupId = null, bool $overwriteExisting = false): array
    {
        // init statistics
        $statistics = ['count' => ['exists' => 0, 'inserted' => 0]];

        // loop data
        foreach ($data as $item) {
            // field checking
            if (!isset($item['email']) || !isset($item['display_name']) || !isset($item['password'])) {
                throw new BackendException(
                    'The .csv file should have the following columns; "email", "password" and "display_name".'
                );
            }

            // define exists
            $exists = self::existsByEmail($item['email']);

            // do not overwrite existing profiles
            if ($exists && !$overwriteExisting) {
                // adding to exists
                $statistics['count']['exists'] += 1;

                // skip this item
                continue;
            }

            // build item
            $values = [
                'email' => $item['email'],
                'registered_on' => BackendModel::getUTCDate(),
                'display_name' => $item['display_name'],
                'url' => self::getUrl($item['display_name']),
            ];

            // does not exist
            if (!$exists) {
                // import
                $id = self::insert($values);

                // update counter
                $statistics['count']['inserted'] += 1;
            } else {
                //  already exists get profile
                $profile = self::getByEmail($item['email']);
                $id = $profile['id'];

                // exists
                $statistics['count']['exists'] += 1;
            }

            // new password filled in?
            if ($item['password']) {
                // build password
                $values['password'] = self::encryptPassword($item['password']);
            }

            // update values
            self::update($id, $values);

            // we have a group id
            if ($groupId !== null) {
                // init values
                $values = [];

                // build item
                $values['profile_id'] = $id;
                $values['group_id'] = $groupId;
                $values['starts_on'] = time();

                // insert values
                self::insertProfileGroup($values);
            }
        }

        return $statistics;
    }

    /**
     * Import multiple profiles based on a given array
     * Each row in the array should contain the fields:
     *  * email
     *  * display_name
     *  * password
     *
     * @return array array('count' => array('exists' => 0, 'inserted' => 0));
     */
    public static function importFromArray(array $data, int $groupId = null, bool $overwriteExisting = false): array
    {
        $statistics = ['count' => ['exists' => 0, 'inserted' => 0]];

        foreach ($data as $item) {
            if (!isset($item['email']) || !isset($item['display_name']) || !isset($item['password'])) {
                throw new BackendException(
                    'The array should have the following fields; "email", "password" and "display_name".'
                );
            }

            $exists = self::existsByEmail($item['email']);

            // do not overwrite existing profiles
            if ($exists && !$overwriteExisting) {
                $statistics['count']['exists'] += 1;
                continue;
            }

            $values = [
                'email' => $item['email'],
                'registered_on' => BackendModel::getUTCDate(),
                'display_name' => $item['display_name'],
                'url' => self::getUrl($item['display_name']),
                'password' => self::encryptPassword(time()), // @remark this is a temporary password, but it can't be null
                'last_login' => date('Y-m-d H:i:s', 0), // @remark, this should be fixed! but at this point the column last_login can't be null
            ];

            if (!$exists) {
                $id = self::insert($values);
                $statistics['count']['inserted'] += 1;
            } else {
                $profile = self::getByEmail($item['email']);
                $id = $profile['id'];
                $statistics['count']['exists'] += 1;
            }

            if ($item['password']) {
                $values['password'] = self::encryptPassword($item['password']);
                self::update($id, $values);
            }

            if ($groupId !== null) {
                $values = [];

                $values['profile_id'] = $id;
                $values['group_id'] = $groupId;
                $values['starts_on'] = BackendModel::getUTCDate();

                self::insertProfileGroup($values);
            }
        }

        return $statistics;
    }

    public static function insert(array $profile): int
    {
        $password = '';
        if (array_key_exists('password', $profile)) {
            $password = $profile['password'];
        }
        $status = Status::active();
        if (array_key_exists('status', $profile)) {
            $status = Status::fromString($profile['status']);
        }

        $profile = new Profile(
            $profile['email'],
            $password,
            $status,
            $profile['display_name'] ?? $profile['displayName'],
            $profile['url']
        );

        BackendModel::get(ProfileRepository::class)->add($profile);

        return $profile->getId();
    }

    public static function insertGroup(array $group): int
    {
        $group = new Group($group['name']);

        BackendModel::get(GroupRepository::class)->add($group);

        return $group->getId();
    }

    /**
     * Add a profile to a group.
     *
     * @param array $membership
     *
     * @return int
     */
    public static function insertProfileGroup(array $membership): int
    {
        $profile = BackendModel::get(ProfileRepository::class)->find($membership['profile_id']);
        $group = BackendModel::get(GroupRepository::class)->find($membership['group_id']);

        $expiresOn = null;
        if (array_key_exists('expires_on', $membership)) {
            $expiresOn = DateTime::createFromFormat('Y-m-d H:i:s', $membership['expires_on']);
        }

        $existingGroupRight = $profile->getRights()->filter(
            function (GroupRight $groupRight) use ($group) {
                return $groupRight->getGroup()->getId() === $group->getId();
            }
        )->first();

        if ($existingGroupRight instanceof GroupRight) {
            $existingGroupRight->update(
                $group,
                DateTime::createFromFormat('Y-m-d H:i:s', $membership['starts_on']),
                null
            );

            BackendModel::get('doctrine.orm.entity_manager')->flush();

            return $existingGroupRight->getId();
        }

        $groupRight = new GroupRight(
            $profile,
            $group,
            DateTime::createFromFormat('Y-m-d H:i:s', $membership['starts_on']),
            $expiresOn
        );

        BackendModel::get(GroupRightRepository::class)->add($groupRight);

        return $groupRight->getId();
    }

    /**
     * Notify admin - after adding profile to profiles module
     *
     * @param array $values
     * @param string $templatePath
     */
    public static function notifyAdmin(array $values, string $templatePath = null): void
    {
        // to email
        $toEmail = BackendModel::get('fork.settings')->get('Profiles', 'profile_notification_email', null);

        if ($toEmail === null) {
            $to = BackendModel::get('fork.settings')->get('Core', 'mailer_to');
            $toEmail = $to['email'];
        }

        // define backend url
        $backendUrl = BackendModel::createUrlForAction('Edit', 'Profiles') . '&id=' . $values['id'];

        // set variables
        $variables = [
            'message' => vsprintf(
                BL::msg('NotificationNewProfileToAdmin', 'Profiles'),
                [
                    $values['display_name'],
                    $values['email'],
                    $backendUrl,
                ]
            ),
        ];

        // define subject
        $subject = vsprintf(
            BL::lbl('NotificationNewProfileToAdmin', 'Profiles'),
            [
                $values['email'],
            ]
        );

        self::sendMail(
            $subject,
            $templatePath,
            $variables,
            $toEmail
        );
    }

    /**
     * Notify profile - after adding profile to profiles module
     *
     * @param array $values
     * @param bool $forUpdate
     * @param string $templatePath
     */
    public static function notifyProfile(
        array $values,
        bool $forUpdate = false,
        string $templatePath = null
    ): void {
        // set variables
        $variables = [
            'message' => vsprintf(
                BL::msg('NotificationNewProfileLoginCredentials', 'Profiles'),
                [
                    $values['email'],
                    $values['unencrypted_password'],
                    SITE_URL,
                ]
            ),
        ];

        // define subject
        $notificationSubject = $forUpdate ? 'NotificationUpdatedProfileToProfile' : 'NotificationNewProfileToProfile';
        $subject = BL::lbl($notificationSubject, 'Profiles');

        self::sendMail(
            $subject,
            $templatePath,
            $variables,
            $values['email'],
            $values['display_name']
        );
    }

    /**
     * Send mail
     *
     * @param string $subject
     * @param string|null $templatePath
     * @param array $variables
     * @param string $toEmail
     * @param string $toDisplayName
     */
    protected static function sendMail(
        $subject,
        ?string $templatePath,
        array $variables,
        string $toEmail,
        string $toDisplayName = null
    ): void {
        if (empty($templatePath)) {
            $templatePath = FRONTEND_CORE_PATH . '/Layout/Templates/Mails/Notification.html.twig';
        }

        // define variables
        $from = BackendModel::get('fork.settings')->get('Core', 'mailer_from');
        $replyTo = BackendModel::get('fork.settings')->get('Core', 'mailer_reply_to');

        // create a message object and set all the needed properties
        $message = Message::newInstance($subject)
            ->setFrom([$from['email'] => $from['name']])
            ->setTo([$toEmail => $toDisplayName])
            ->setReplyTo([$replyTo['email'] => $replyTo['name']])
            ->parseHtml($templatePath, $variables, true);

        // send it through the mailer service
        BackendModel::get('mailer')->send($message);
    }

    /**
     * Insert or update a single profile setting.
     *
     * @param int $profileId Profile id.
     * @param string $name Setting name.
     * @param mixed $value Setting value.
     */
    public static function setSetting(int $profileId, string $name, $value): void
    {
        $SettingRepository = BackendModel::get(SettingRepository::class);

        $profile = BackendModel::get(ProfileRepository::class)->find($profileId);

        $existingSetting = $SettingRepository->findOneBy(
            [
                'profile' => $profile,
                'name' => $name,
            ]
        );

        if ($existingSetting instanceof Setting) {
            $existingSetting->update($value);

            BackendModel::get('doctrine.orm.default_entity_manager')->flush();

            return;
        }

        $setting = new Setting($profile, $name, $value);
        $SettingRepository->add($setting);
    }

    /**
     * Update a profile.
     *
     * @param int $profileId The profile id.
     * @param array $profile The values to update.
     *
     * @return int
     */
    public static function update(int $profileId, array $profile): int
    {
        $profileEntity = BackendModel::get(ProfileRepository::class)->find($profileId);

        if (!$profileEntity instanceof Profile) {
            return $profileId;
        }

        $email = $profileEntity->getEmail();
        if (array_key_exists('email', $profile)) {
            $email = $profile['email'];
        }
        $password = $profileEntity->getPassword();
        if (array_key_exists('password', $profile)) {
            $password = $profile['password'];
        }
        $status = $profileEntity->getStatus();
        if (array_key_exists('status', $profile)) {
            $status = Status::fromString($profile['status']);
        }
        $displayName = $profileEntity->getDisplayName();
        if (array_key_exists('display_name', $profile)) {
            $displayName = $profile['display_name'];
        }
        $url = $profileEntity->getUrl();
        if (array_key_exists('url', $profile)) {
            $url = $profile['url'];
        }

        $profileEntity->update(
            $email,
            $password,
            $status,
            $displayName,
            $url
        );

        BackendModel::get('doctrine.orm.entity_manager')->flush();

        return $profileId;
    }

    public static function updateGroup(int $groupId, array $group): int
    {
        $groupEntity = BackendModel::get(GroupRepository::class)->find($groupId);

        if (!$groupEntity instanceof Group) {
            return $groupId;
        }

        $groupEntity->update($group['name']);

        BackendModel::get('doctrine.orm.entity_manager')->flush();

        return $groupId;
    }

    /**
     * Update a membership of a profile in a group.
     *
     * @param int $membershipId
     * @param array $membership
     *
     * @return int
     */
    public static function updateProfileGroup(int $membershipId, array $membership): int
    {
        $groupRight = BackendModel::get(GroupRightRepository::class)->find($membershipId);

        if (!$groupRight instanceof GroupRight) {
            return $membershipId;
        }

        $group = $groupRight->getGroup();
        if (array_key_exists('group_id', $membership)) {
            $group = BackendModel::get(GroupRepository::class)->find($membership['group_id']);
        }
        $expiresOn = $groupRight->getExpiryDate();
        if (array_key_exists('expires_on', $membership)) {
            $expiresOn = DateTime::createFromFormat('U', $membership['expires_on']);
        }
        $startsOn = $groupRight->getStartDate();
        if (array_key_exists('starts_on', $membership)) {
            $expiresOn = DateTime::createFromFormat('U', $membership['starts_on']);
        }

        $groupRight->update($group, $startsOn, $expiresOn);

        BackendModel::get('doctrine.orm.entity_manager')->flush();

        return $membershipId;
    }
}
