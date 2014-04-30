<?php

namespace Backend\Modules\Profiles\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Uri as CommonUri;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * In this file we store all generic functions that we will be using in the profiles module.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class Model
{
    /**
     * Cache avatars
     *
     * @param string
     */
    protected static $avatars;

    /**
     * Browse groups for datagrid.
     *
     * @var    string
     */
    const QRY_DATAGRID_BROWSE_PROFILE_GROUPS =
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
    public static function delete($ids)
    {
        // init db
        $db = BackendModel::getContainer()->get('database');

        // redefine
        $ids = (array) $ids;

        // delete profiles
        foreach ($ids as $id) {
            // redefine
            $id = (int) $id;

            // delete sessions
            $db->delete('profiles_sessions', 'profile_id = ?', $id);

            // set profile status to deleted
            self::update($id, array('status' => 'deleted'));
        }
    }

    /**
     * Delete a profile group.
     *
     * @param int $id Id of the group.
     */
    public static function deleteGroup($id)
    {
        // redefine
        $id = (int) $id;

        // delete rights
        BackendModel::getContainer()->get('database')->delete('profiles_groups_rights', 'group_id = ?', $id);

        // delete group
        BackendModel::getContainer()->get('database')->delete('profiles_groups', 'id = ?', $id);
    }

    /**
     * Delete a membership of a profile in a group.
     *
     * @param int $id Id of the membership.
     */
    public static function deleteProfileGroup($id)
    {
        BackendModel::getContainer()->get('database')->delete('profiles_groups_rights', 'id = ?', (int) $id);
    }

    /**
     * Delete a sessions of a profile.
     *
     * @param int $id Profile id.
     */
    public static function deleteSession($id)
    {
        BackendModel::getContainer()->get('database')->delete('profiles_sessions', 'profile_id = ?', (int) $id);
    }

    /**
     * Check if a profile exists.
     *
     * @param int $id Profile id.
     * @return bool
     */
    public static function exists($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles AS p
             WHERE p.id = ?
             LIMIT 1',
            (int) $id
        );
    }

    /**
     * Check if a profile exists by email address.
     *
     * @param string $email Email address to check for existence.
     * @param int    $id    Profile id to ignore.
     * @return bool
     */
    public static function existsByEmail($email, $id = null)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles AS p
             WHERE p.email = ? AND p.id != ?
             LIMIT 1',
            array((string) $email, (int) $id)
        );
    }

    /**
     * Check if a display name exists.
     *
     * @param string $displayName The display name to check.
     * @param int    $id          Profile id to ignore.
     * @return bool
     */
    public static function existsDisplayName($displayName, $id = null)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles AS p
             WHERE p.display_name = ? AND p.id != ?
             LIMIT 1',
            array((string) $displayName, (int) $id)
        );
    }

    /**
     * Check if a group exists.
     *
     * @param int $id Group id.
     * @return bool
     */
    public static function existsGroup($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles_groups AS pg
             WHERE pg.id = ?
             LIMIT 1',
            (int) $id
        );
    }

    /**
     * Check if a group name exists.
     *
     * @param string $groupName Group name.
     * @param int    $id        Group id to ignore.
     * @return bool
     */
    public static function existsGroupName($groupName, $id = null)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles_groups AS pg
             WHERE pg.name = ? AND pg.id != ?
             LIMIT 1',
            array((string) $groupName, (int) $id)
        );
    }

    /**
     * Check if a profile is in a group.
     *
     * @param int $id Membership id.
     * @return bool
     */
    public static function existsProfileGroup($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM profiles_groups_rights AS gr
             WHERE gr.id = ?
             LIMIT 1',
            (int) $id
        );
    }

    /**
     * Get information about a profile.
     *
     * @param int $id The profile id to get the information for.
     * @return array
     */
    public static function get($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT p.id, p.email, p.status, p.display_name, p.url
             FROM profiles AS p
             WHERE p.id = ?',
            (int) $id
        );
    }

    /**
     * Get avatar
     *
     * @param int    $id    The id for the profile we want to get the avatar from.
     * @param string $email The email from the user we can use for gravatar.
     * @return string $avatar            The absolute path to the avatar.
     */
    public static function getAvatar($id, $email = null)
    {
        // redefine id
        $id = (int) $id;

        // return avatar from cache
        if (isset(self::$avatars[$id])) {
            return self::$avatars[$id];
        }

        // define avatar path
        $avatarPath = FRONTEND_FILES_URL . '/Profiles/Avatars/32x32/';

        // get avatar for profile
        $avatar = self::getSetting($id, 'avatar');

        // if no email is given
        if (!$email) {
            // get user
            $user = self::get($id);

            // redefine email
            $email = $user['email'];
        }

        // no custom avatar defined, get gravatar if allowed
        if (empty($avatar) && BackendModel::getModuleSetting('Profiles', 'allow_gravatar', true)) {
            // define hash
            $hash = md5(strtolower(trim('d' . $email)));

            // define avatar url
            $avatar = 'http://www.gravatar.com/avatar/' . $hash;

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
        self::$avatars[$id] = $avatar;

        // return avatar image path
        return $avatar;
    }

	/**
	 * Get information about a profile, by email
	 *
	 * @param  string $email The profile email to get the information for.
	 * @return array
	 */
	public static function getByEmail($email)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT p.id, p.email, p.status, p.display_name, p.url
			 FROM profiles AS p
			 WHERE p.email = ?',
			(string) $email
		);
	}

    /**
     * Encrypt a string with a salt.
     *
     * @param string $string String to encrypt.
     * @param string $salt   Salt to saltivy the string with.
     * @return string
     */
    public static function getEncryptedString($string, $salt)
    {
        return md5(sha1(md5((string) $string)) . sha1(md5((string) $salt)));
    }

    /**
     * Get information about a profile group.
     *
     * @param int $id Id of the group.
     * @return array
     */
    public static function getGroup($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT pg.id, pg.name
             FROM profiles_groups AS pg
             WHERE pg.id = ?',
            (int) $id
        );
    }

    /**
     * Get the list of all groups as array($groupId => $groupName).
     *
     * @return array
     */
    public static function getGroups()
    {
        return (array) BackendModel::getContainer()->get('database')->getPairs(
            'SELECT id, name FROM profiles_groups ORDER BY name'
        );
    }

    /**
     * Get profile groups for dropdown not yet linked to a profile
     *
     * @param int $profileId Profile id.
     * @param int $includeId Group id to always include.
     * @return array
     */
    public static function getGroupsForDropDown($profileId, $includeId = null)
    {
        // init db
        $db = BackendModel::getContainer()->get('database');

        // get groups already linked but don't include the includeId
        if ($includeId !== null) {
            $groupIds = (array) $db->getColumn(
                'SELECT group_id
                 FROM profiles_groups_rights
                 WHERE profile_id = ? AND id != ?',
                array($profileId, $includeId)
            );
        } else {
            $groupIds = (array) $db->getColumn(
                'SELECT group_id
                 FROM profiles_groups_rights
                 WHERE profile_id = ?',
                (int) $profileId
            );
        }

        // get groups not yet linked
        return (array) $db->getPairs(
            'SELECT id, name
             FROM profiles_groups
             WHERE id NOT IN(\'' . implode('\',\'', $groupIds) . '\')'
        );
    }

    /**
     * Get information about a profile group where a user is member of.
     *
     * @param int $id Membership id.
     * @return array
     */
    public static function getProfileGroup($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT gr.id, gr.profile_id, g.id AS group_id, g.name, UNIX_TIMESTAMP(gr.expires_on) AS expires_on
             FROM profiles_groups_rights AS gr
             INNER JOIN profiles_groups AS g ON g.id = gr.group_id
             WHERE gr.id = ?',
            (int) $id
        );
    }

    /**
     * Get the groups where a profile is member of.
     *
     * @param int $id The profile id to get the groups for.
     * @return array
     */
    public static function getProfileGroups($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT gr.id, gr.group_id, g.name AS group_name, gr.expires_on
             FROM profiles_groups AS g
             INNER JOIN profiles_groups_rights AS gr ON gr.group_id = g.id
             WHERE gr.profile_id = ?',
            (int) $id
        );
    }

    /**
     * Generate a random string.
     *
     * @param int  $length    Length of random string.
     * @param bool $numeric   Use numeric characters.
     * @param bool $lowercase Use alphanumeric lowercase characters.
     * @param bool $uppercase Use alphanumeric uppercase characters.
     * @param bool $special   Use special characters.
     * @return string
     */
    public static function getRandomString(
        $length = 15,
        $numeric = true,
        $lowercase = true,
        $uppercase = true,
        $special = true
    ) {
        // init
        $characters = '';
        $string = '';

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
        for ($i = 0; $i < $length; $i++) {
            // random index
            $index = mt_rand(0, strlen($characters));

            // add character to salt
            $string .= mb_substr($characters, $index, 1, SPOON_CHARSET);
        }

        // cough up
        return $string;
    }

    /**
     * Get a setting for a profile.
     *
     * @param int    $id   Profile id.
     * @param string $name Setting name.
     * @return string
     */
    public static function getSetting($id, $name)
    {
        return unserialize(
            (string) BackendModel::getContainer()->get('database')->getVar(
                'SELECT ps.value
                 FROM profiles_settings AS ps
                 WHERE ps.profile_id = ? AND ps.name = ?',
                array((int) $id, (string) $name)
            )
        );
    }

    /**
     * Fetch the list of status, but for a dropdown.
     *
     * @return array
     */
    public static function getStatusForDropDown()
    {
        // fetch types
        $status = BackendModel::getContainer()->get('database')->getEnumValues('profiles', 'status');

        // init
        $labels = $status;

        // loop and build labels
        foreach ($labels as &$row) {
            $row = \SpoonFilter::ucfirst(BL::getLabel(\SpoonFilter::ucfirst($row)));
        }

        // build array
        return array_combine($status, $labels);
    }

    /**
     * Retrieve a unique URL for a profile based on the display name.
     *
     * @param string $displayName The display name to base on.
     * @param int    $id          The id of the profile to ignore.
     * @return string
     */
    public static function getUrl($displayName, $id = null)
    {
        // decode specialchars
        $displayName = \SpoonFilter::htmlspecialcharsDecode((string) $displayName);

        // urlise
        $url = CommonUri::getUrl($displayName);

        // get db
        $db = BackendModel::getContainer()->get('database');

        // new item
        if ($id === null) {
            // get number of profiles with this URL
            $number = (int) $db->getVar(
                'SELECT 1
                 FROM profiles AS p
                 WHERE p.url = ?
                 LIMIT 1',
                (string) $url
            );

            // already exists
            if ($number != 0) {
                // add number
                $url = BackendModel::addNumber($url);

                // try again
                return self::getURL($url);
            }
        } else {
            // get number of profiles with this URL
            $number = (int) $db->getVar(
                'SELECT 1
                 FROM profiles AS p
                 WHERE p.url = ? AND p.id != ?
                 LIMIT 1',
                array((string) $url, (int) $id)
            );

            // already exists
            if ($number != 0) {
                // add number
                $url = BackendModel::addNumber($url);

                // try again
                return self::getURL($url, $id);
            }
        }

        // cough up new url
        return $url;
    }

    /**
     * Get the HTML for a user to use in a datagrid
     *
     * @param int $id The Id of the user.
     * @return string
     */
    public static function getUser($id)
    {
        $id = (int) $id;

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
        $html .= '	<div class="avatar av24">' . "\n";
        if ($allowed) {
            $html .= '		<a href="' .
                     BackendModel::createURLForAction(
                         'Edit',
                         'Profiles'
                     ) . '&amp;id=' . $id . '">' . "\n";
        }
        $html .= '			<img src="' . $avatar . '" width="24" height="24" alt="' . $nickname . '" />' . "\n";
        if ($allowed) {
            $html .= '		</a>' . "\n";
        }
        $html .= '	</div>';
        $html .= '	<p><a href="' .
                 BackendModel::createURLForAction(
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
	 * @param int[optional] $groupId Adding these profiles to a group
	 * @param bool[optional] $overwriteExisting If set to true, this will overwrite existing profiles
	 * @param return array('count' => array('exists' => 0, 'inserted' => 0));
	 */
	public static function importCsv($data, $groupId = null, $overwriteExisting = false)
	{
		// init statistics
		$statistics = array('count' => array('exists' => 0, 'inserted' => 0));

		// loop data
		foreach ($data as $item) {
			// field checking
			if (!isset($item['email']) || !isset($item['display_name']) || !isset($item['password'])) {
				throw new BackendException('The .csv file should have the following columns; "email", "password" and "display_name".');
			}

			// init $insert
			$values = array();

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
			$values = array(
				'email' => $item['email'],
				'registered_on' => BackendModel::getUTCDate(),
				'display_name' => $item['display_name'],
				'url' => self::getUrl($item['display_name'])
			);

			// does not exists
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
				// get new salt
				$salt = self::getRandomString();

				// update salt
				self::setSetting($id, 'salt', $salt);

				// build password
				$values['password'] = self::getEncryptedString($item['password'], $salt);
			}

			// update values
			self::update($id, $values);

			// we have a group id
			if ($groupId) {
				// init values
				$values = array();
			
				// build item
				$values['profile_id'] = $id;
				$values['group_id'] = $groupId;
				$values['starts_on'] = BackendModel::getUTCDate();

				// insert values
				$id = self::insertProfileGroup($values);
			}
		}

		return $statistics;
	}

    /**
     * Insert a new profile.
     *
     * @param array $values The values to insert.
     * @return int
     */
    public static function insert(array $values)
    {
        return (int) BackendModel::getContainer()->get('database')->insert('profiles', $values);
    }

    /**
     * Insert a new group.
     *
     * @param array $values Group data.
     * @return int
     */
    public static function insertGroup(array $values)
    {
        return (int) BackendModel::getContainer()->get('database')->insert('profiles_groups', $values);
    }

    /**
     * Add a profile to a group.
     *
     * @param array $values Membership data.
     * @return int
     */
    public static function insertProfileGroup(array $values)
    {
        return (int) BackendModel::getContainer()->get('database')->insert('profiles_groups_rights', $values);
    }

    /**
     * Insert or update a single profile setting.
     *
     * @param int    $id    Profile id.
     * @param string $name  Setting name.
     * @param mixed  $value Setting value.
     */
    public static function setSetting($id, $name, $value)
    {
        BackendModel::getContainer()->get('database')->execute(
            'INSERT INTO profiles_settings(profile_id, name, value)
             VALUES(?, ?, ?)
             ON DUPLICATE KEY UPDATE value = ?',
            array((int) $id, $name, serialize($value), serialize($value))
        );
    }

    /**
     * Update a profile.
     *
     * @param int   $id     The profile id.
     * @param array $values The values to update.
     * @return int
     */
    public static function update($id, array $values)
    {
        return (int) BackendModel::getContainer()->get('database')->update('profiles', $values, 'id = ?', (int) $id);
    }

    /**
     * Update a profile group.
     *
     * @param int   $id     Group id.
     * @param array $values Group data.
     * @return int
     */
    public static function updateGroup($id, array $values)
    {
        return (int) BackendModel::getContainer()->get('database')->update(
            'profiles_groups',
            $values,
            'id = ?',
            (int) $id
        );
    }

    /**
     * Update a membership of a profile in a group.
     *
     * @param int   $id     Membership id.
     * @param array $values Membership data.
     * @return int
     */
    public static function updateProfileGroup($id, array $values)
    {
        return (int) BackendModel::getContainer()->get('database')->update(
            'profiles_groups_rights',
            $values,
            'id = ?',
            (int) $id
        );
    }
}
