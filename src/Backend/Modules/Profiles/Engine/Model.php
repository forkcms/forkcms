<?php

namespace Backend\Modules\Profiles\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Mailer\Message;
use Common\Uri as CommonUri;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Exception as BackendException;

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
     *
     * @var string
     */
    const QUERY_DATAGRID_BROWSE_PROFILE_GROUPS =
        'SELECT gr.id, g.name AS group_name, UNIX_TIMESTAMP(gr.expires_on) AS expires_on
         FROM profiles_groups AS g
         INNER JOIN profiles_groups_rights AS gr ON gr.group_id = g.id AND
            (gr.expires_on IS NULL OR gr.expires_on > NOW())
         WHERE gr.profile_id = ?';

    /**
     * Delete the given profiles.
     *
     * @param mixed $ids One ID, or an array of IDs.
     */
    public static function delete($ids): void
    {
        // init database
        $database = BackendModel::getContainer()->get('database');

        // redefine
        $ids = (array) $ids;

        // delete profiles
        foreach ($ids as $id) {
            // redefine
            $id = (int) $id;

            // delete sessions
            $database->delete('profiles_sessions', 'profile_id = ?', $id);

            // set profile status to deleted
            self::update($id, ['status' => 'deleted']);
        }
    }

    public static function deleteGroup(int $groupId): void
    {
        // delete rights
        BackendModel::getContainer()->get('database')->delete('profiles_groups_rights', 'group_id = ?', $groupId);

        // delete group
        BackendModel::getContainer()->get('database')->delete('profiles_groups', 'id = ?', $groupId);
    }

    /**
     * Delete a membership of a profile in a group.
     *
     * @param int $membershipId Id of the membership.
     */
    public static function deleteProfileGroup(int $membershipId)
    {
        BackendModel::getContainer()->get('database')->delete('profiles_groups_rights', 'id = ?', $membershipId);
    }

    public static function deleteSession(int $id): void
    {
        BackendModel::getContainer()->get('database')->delete('profiles_sessions', 'profile_id = ?', $id);
    }

    public static function exists(int $profileId): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles AS p
             WHERE p.id = ?
             LIMIT 1',
            $profileId
        );
    }

    public static function existsByEmail(string $email, int $excludedProfileId = 0): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles AS p
             WHERE p.email = ? AND p.id != ?
             LIMIT 1',
            [$email, $excludedProfileId]
        );
    }

    public static function existsDisplayName(string $displayName, int $excludedProfileId = 0): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles AS p
             WHERE p.display_name = ? AND p.id != ?
             LIMIT 1',
            [$displayName, $excludedProfileId]
        );
    }

    public static function existsGroup(int $groupId): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles_groups AS pg
             WHERE pg.id = ?
             LIMIT 1',
            $groupId
        );
    }

    public static function existsGroupName(string $groupName, int $excludedGroupId = 0): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles_groups AS pg
             WHERE pg.name = ? AND pg.id != ?
             LIMIT 1',
            [$groupName, $excludedGroupId]
        );
    }

    public static function existsProfileGroup(int $membershipId): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles_groups_rights AS gr
             WHERE gr.id = ?
             LIMIT 1',
            $membershipId
        );
    }

    public static function get(int $profileId): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT p.id, p.email, p.status, p.display_name, p.url
             FROM profiles AS p
             WHERE p.id = ?',
            $profileId
        );
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
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT p.id, p.email, p.status, p.display_name, p.url
             FROM profiles AS p
             WHERE p.email = ?',
            $email
        );
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
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT pg.id, pg.name
             FROM profiles_groups AS pg
             WHERE pg.id = ?',
            $groupId
        );
    }

    /**
     * Get the list of all groups as array($groupId => $groupName).
     *
     * @return array
     */
    public static function getGroups(): array
    {
        return (array) BackendModel::getContainer()->get('database')->getPairs(
            'SELECT id, name FROM profiles_groups ORDER BY name'
        );
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
        // init database
        $database = BackendModel::getContainer()->get('database');

        // get groups already linked but don't include the includeId
        if ($includeId !== null) {
            $groupIds = (array) $database->getColumn(
                'SELECT group_id
                 FROM profiles_groups_rights
                 WHERE profile_id = ? AND id != ?',
                [$profileId, $includeId]
            );
        } else {
            $groupIds = (array) $database->getColumn(
                'SELECT group_id
                 FROM profiles_groups_rights
                 WHERE profile_id = ?',
                (int) $profileId
            );
        }

        // get groups not yet linked
        return (array) $database->getPairs(
            'SELECT id, name
             FROM profiles_groups
             WHERE id NOT IN(\'' . implode('\',\'', $groupIds) . '\')'
        );
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
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT gr.id, gr.profile_id, g.id AS group_id, g.name, UNIX_TIMESTAMP(gr.expires_on) AS expires_on
             FROM profiles_groups_rights AS gr
             INNER JOIN profiles_groups AS g ON g.id = gr.group_id
             WHERE gr.id = ?',
            $membershipId
        );
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
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT gr.id, gr.group_id, g.name AS group_name, gr.expires_on
             FROM profiles_groups AS g
             INNER JOIN profiles_groups_rights AS gr ON gr.group_id = g.id
             WHERE gr.profile_id = ?',
            $profileId
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
        return unserialize(
            (string) BackendModel::getContainer()->get('database')->getVar(
                'SELECT ps.value
                 FROM profiles_settings AS ps
                 WHERE ps.profile_id = ? AND ps.name = ?',
                [$profileId, $name]
            )
        );
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

        // get database
        $database = BackendModel::getContainer()->get('database');

        // new item
        if ($excludedProfileId === null) {
            // get number of profiles with this URL
            $number = (int) $database->getVar(
                'SELECT 1
                 FROM profiles AS p
                 WHERE p.url = ?
                 LIMIT 1',
                $url
            );

            // already exists
            if ($number != 0) {
                // add number
                $url = BackendModel::addNumber($url);

                // try again
                return self::getUrl($url);
            }
        } else {
            // get number of profiles with this URL
            $number = (int) $database->getVar(
                'SELECT 1
                 FROM profiles AS p
                 WHERE p.url = ? AND p.id != ?
                 LIMIT 1',
                [$url, $excludedProfileId]
            );

            // already exists
            if ($number != 0) {
                // add number
                $url = BackendModel::addNumber($url);

                // try again
                return self::getUrl($url, $excludedProfileId);
            }
        }

        // cough up new url
        return $url;
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
            $html .= '      <a href="' .
                     BackendModel::createUrlForAction(
                         'Edit',
                         'Profiles'
                     ) . '&amp;id=' . $id . '">' . "\n";
        }
        $html .= '          <img src="' . $avatar . '" width="24" height="24" alt="' . $nickname . '" />' . "\n";
        if ($allowed) {
            $html .= '      </a>' . "\n";
        }
        $html .= '  </div>';
        $html .= '  <p><a href="' .
                 BackendModel::createUrlForAction(
                     'Edit',
                     'Profiles'
                 ) . '&amp;id=' . $id . '">' . $nickname . '</a></p>' . "\n";
        $html .= '</div>';

        return $html;
    }

    /**
     * Import CSV data
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
                // already exists
            } else {
                // get profile
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
                $values['starts_on'] = BackendModel::getUTCDate();

                // insert values
                self::insertProfileGroup($values);
            }
        }

        return $statistics;
    }

    public static function insert(array $profile): int
    {
        return (int) BackendModel::getContainer()->get('database')->insert('profiles', $profile);
    }

    public static function insertGroup(array $group): int
    {
        return (int) BackendModel::getContainer()->get('database')->insert('profiles_groups', $group);
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
        return (int) BackendModel::getContainer()->get('database')->insert('profiles_groups_rights', $membership);
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
        BackendModel::getContainer()->get('database')->execute(
            'INSERT INTO profiles_settings(profile_id, name, value)
             VALUES(?, ?, ?)
             ON DUPLICATE KEY UPDATE value = ?',
            [$profileId, $name, serialize($value), serialize($value)]
        );
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
        return (int) BackendModel::getContainer()->get('database')->update('profiles', $profile, 'id = ?', $profileId);
    }

    public static function updateGroup(int $profileId, array $group): int
    {
        return (int) BackendModel::getContainer()->get('database')->update(
            'profiles_groups',
            $group,
            'id = ?',
            $profileId
        );
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
        return (int) BackendModel::getContainer()->get('database')->update(
            'profiles_groups_rights',
            $membership,
            'id = ?',
            $membershipId
        );
    }
}
