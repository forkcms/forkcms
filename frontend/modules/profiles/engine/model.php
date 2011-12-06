<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using with profiles.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jan Moesen <jan.moesen@netlash.com>
 */
class FrontendProfilesModel
{
	const MAX_DISPLAY_NAME_CHANGES = 2;

	/**
	 * Delete a setting.
	 *
	 * @param int $id Profile id.
	 * @param string $name Setting name.
	 * @return int
	 */
	public static function deleteSetting($id, $name)
	{
		return (int) FrontendModel::getDB(true)->delete('profiles_settings', 'profile_id = ? AND name = ?', array((int) $id, (string) $name));
	}

	/**
	 * Check if a profile exists by email address.
	 *
	 * @param string $email Email to check for existence.
	 * @param int[optional] $ignoreId Profile id to ignore.
	 * @return bool
	 */
	public static function existsByEmail($email, $ignoreId = null)
	{
		return (bool) FrontendModel::getDB()->getVar(
			'SELECT COUNT(p.id)
			 FROM profiles AS p
			 WHERE p.email = ? AND p.id != ?',
			array((string) $email, (int) $ignoreId)
		);
	}

	/**
	 * Check if a display name exists.
	 *
	 * @param string $displayName Display name to check for existence.
	 * @param int[optional] $id Profile id to ignore.
	 * @return bool
	 */
	public static function existsDisplayName($displayName, $id = null)
	{
		return (bool) FrontendModel::getDB()->getVar(
			'SELECT COUNT(p.id)
			 FROM profiles AS p
			 WHERE p.id != ? AND p.display_name = ?',
			array((int) $id, (string) $displayName)
		);
	}

	/**
	 * Get profile by its id.
	 *
	 * @param int $profileId Id of the wanted profile.
	 * @return FrontendProfilesProfile
	 */
	public static function get($profileId)
	{
		return new FrontendProfilesProfile((int) $profileId);
	}

	/**
	 * Get an encrypted string.
	 *
	 * @param string $string String to encrypt.
	 * @param string $salt Salt to add to the string.
	 * @return string
	 */
	public static function getEncryptedString($string, $salt)
	{
		return md5(sha1(md5((string) $string)) . sha1(md5((string) $salt)));
	}

	/**
	 * Get profile id by email.
	 *
	 * @param string $email Email address.
	 * @return int
	 */
	public static function getIdByEmail($email)
	{
		return (int) FrontendModel::getDB()->getVar('SELECT p.id FROM profiles AS p WHERE p.email = ?', (string) $email);
	}

	/**
	 * Get profile id by setting.
	 *
	 * @param string $name Setting name.
	 * @param string $value Value of the setting.
	 * @return int
	 */
	public static function getIdBySetting($name, $value)
	{
		return (int) FrontendModel::getDB()->getVar(
			'SELECT ps.profile_id
			 FROM profiles_settings AS ps
			 WHERE ps.name = ? AND ps.value = ?',
			array((string) $name, serialize((string) $value))
		);
	}

	/**
	 * Generate a random string.
	 *
	 * @param int[optional] $length Length of random string.
	 * @param bool[optional] $numeric Use numeric characters.
	 * @param bool[optional] $lowercase Use alphanumeric lowercase characters.
	 * @param bool[optional] $uppercase Use alphanumeric uppercase characters.
	 * @param bool[optional] $special Use special characters.
	 * @return string
	 */
	public static function getRandomString($length = 15, $numeric = true, $lowercase = true, $uppercase = true, $special = true)
	{
		// init
		$characters = '';
		$string = '';

		// possible characters
		if($numeric) $characters .= '1234567890';
		if($lowercase) $characters .= 'abcdefghijklmnopqrstuvwxyz';
		if($uppercase) $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		if($special) $characters .= '-_.:;,?!@#&=)([]{}*+%$';

		// get random characters
		for($i = 0; $i < $length; $i++)
		{
			// random index
			$index = mt_rand(0, strlen($characters));

			// add character to salt
			$string .= mb_substr($characters, $index, 1, SPOON_CHARSET);
		}

		return $string;
	}

	/**
	 * Get a setting for a profile.
	 *
	 * @param int $id Profile id.
	 * @param string $name Setting name.
	 * @return string
	 */
	public static function getSetting($id, $name)
	{
		return unserialize((string) FrontendModel::getDB()->getVar(
			'SELECT ps.value
			 FROM profiles_settings AS ps
			 WHERE ps.profile_id = ? AND ps.name = ?',
			array((int) $id, (string) $name))
		);
	}

	/**
	 * Get all settings for a profile.
	 *
	 * @param int $id Profile id.
	 * @return array
	 */
	public static function getSettings($id)
	{
		// get settings
		$settings = (array) FrontendModel::getDB()->getPairs(
			'SELECT ps.name, ps.value
			 FROM profiles_settings AS ps
			 WHERE ps.profile_id = ?',
			(int) $id
		);

		// unserialize values
		foreach($settings as $key => &$value) $value = unserialize($value);

		// return
		return $settings;
	}

	/**
	 * Retrieve a unique URL for a profile based on the display name.
	 *
	 * @param string $displayName The display name to base on.
	 * @param int[optional] $id The id of the profile to ignore.
	 * @return string
	 */
	public static function getUrl($displayName, $id = null)
	{
		// decode specialchars
		$displayName = SpoonFilter::htmlspecialcharsDecode((string) $displayName);

		// urlise
		$url = (string) SpoonFilter::urlise($displayName);

		// get db
		$db = FrontendModel::getDB();

		// new item
		if($id === null)
		{
			// get number of profiles with this URL
			$number = (int) $db->getVar(
				'SELECT COUNT(p.id)
				 FROM profiles AS p
				 WHERE p.url = ?',
				(string) $url
			);

			// already exists
			if($number != 0)
			{
				// add number
				$url = FrontendModel::addNumber($url);

				// try again
				return self::getURL($url);
			}
		}

		// current profile should be excluded
		else
		{
			// get number of profiles with this URL
			$number = (int) $db->getVar(
				'SELECT COUNT(p.id)
				 FROM profiles AS p
				 WHERE p.url = ? AND p.id != ?',
				array((string) $url, (int) $id)
			);

			// already exists
			if($number != 0)
			{
				// add number
				$url = FrontendModel::addNumber($url);

				// try again
				return self::getURL($url, $id);
			}
		}

		return $url;
	}

	/**
	 * Insert a new profile.
	 *
	 * @param array $values Profile data.
	 * @return int
	 */
	public static function insert(array $values)
	{
		return (int) FrontendModel::getDB(true)->insert('profiles', $values);
	}

	/**
	 * Parse the general profiles info into the template.
	 */
	public static function parse()
	{
		// get the template
		$tpl = Spoon::get('template');

		// logged in
		if(FrontendProfilesAuthentication::isLoggedIn())
		{
			// get profile
			$profile = FrontendProfilesAuthentication::getProfile();

			// display name set?
			if($profile->getDisplayName() != '') $tpl->assign('profileDisplayName', $profile->getDisplayName());

			// no display name -> use email
			else $tpl->assign('profileDisplayName', $profile->getEmail());

			// show logged in
			$tpl->assign('isLoggedIn', true);
		}

		// ignore these url's in the querystring
		$ignoreUrls = array(
			FrontendNavigation::getURLForBlock('profiles', 'login'),
			FrontendNavigation::getURLForBlock('profiles', 'register'),
			FrontendNavigation::getURLForBlock('profiles', 'forgot_password')
		);

		// querystring
		$queryString = (isset($_GET['queryString'])) ? SITE_URL . '/' . urldecode($_GET['queryString']) : SELF;

		// check all ignore urls
		foreach($ignoreUrls as $url)
		{
			// querystring contains a boeboe url
			if(stripos($queryString, $url) !== false)
			{
				$queryString = '';
				break;
			}
		}

		// no need to add this if its empty
		$queryString = ($queryString != '') ? '?queryString=' . urlencode($queryString) : '';

		// useful urls
		$tpl->assign('loginUrl', FrontendNavigation::getURLForBlock('profiles', 'login') . $queryString);
		$tpl->assign('registerUrl', FrontendNavigation::getURLForBlock('profiles', 'register'));
		$tpl->assign('forgotPasswordUrl', FrontendNavigation::getURLForBlock('profiles', 'forgot_password'));
	}

	/**
	 * Insert or update a single profile setting.
	 *
	 * @param int $id Profile id.
	 * @param string $name Setting name.
	 * @param mixed $value New setting value.
	 */
	public static function setSetting($id, $name, $value)
	{
		// insert or update
		FrontendModel::getDB(true)->execute(
			'INSERT INTO profiles_settings(profile_id, name, value)
			 VALUES(?, ?, ?)
			 ON DUPLICATE KEY UPDATE value = ?',
			array((int) $id, $name, serialize($value), serialize($value))
		);
	}

	/**
	 * Insert or update multiple profile settings.
	 *
	 * @param int $id Profile id.
	 * @param array $values Settings in key=>valye form.
	 */
	public static function setSettings($id, array $values)
	{
		// go over settings
		foreach($values as $key => $value) self::setSetting($id, $key, $value);
	}

	/**
	 * Update a profile.
	 *
	 * @param int $id The profile id.
	 * @param array $values The values to update.
	 * @return int
	 */
	public static function update($id, array $values)
	{
		return (int) FrontendModel::getDB(true)->update('profiles', $values, 'id = ?', (int) $id);
	}
}
