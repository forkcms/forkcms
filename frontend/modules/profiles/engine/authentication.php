<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Profile authentication functions.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jan Moesen <jan.moesen@netlash.com>
 */
class FrontendProfilesAuthentication
{
	/**
	 * The login credentials are correct and the profile is active.
	 *
	 * @var	string
	 */
	const LOGIN_ACTIVE = 'active';

	/**
	 * The login credentials are correct, but the profile is inactive.
	 *
	 * @var	string
	 */
	const LOGIN_INACTIVE = 'inactive';

	/**
	 * The login credentials are correct, but the profile has been deleted.
	 *
	 * @var	string
	 */
	const LOGIN_DELETED = 'deleted';

	/**
	 * The login credentials are correct, but the profile has been blocked.
	 *
	 * @var	string
	 */
	const LOGIN_BLOCKED = 'blocked';

	/**
	 * The login credentials are incorrect or the profile does not exist.
	 *
	 * @var	string
	 */
	const LOGIN_INVALID = 'invalid';

	/**
	 * The current logged in profile.
	 *
	 * @var	FrontendProfilesProfile
	 */
	private static $profile;

	/**
	 * Cleanup old session records in the database.
	 */
	public static function cleanupOldSessions()
	{
		// remove all sessions with date older then 1 month
		FrontendModel::getContainer()->get('database')->delete('profiles_sessions', 'date <= DATE_SUB(NOW(), INTERVAL 1 MONTH)');
	}

	/**
	 * Get the login/profile status for the given e-mail and password.
	 *
	 * @param string $email Profile email address.
	 * @param string $password Profile password.
	 * @return string One of the FrontendProfilesAuthentication::LOGIN_* constants.
	 */
	public static function getLoginStatus($email, $password)
	{
		$email = (string) $email;
		$password = (string) $password;

		// get profile id
		$profileId = FrontendProfilesModel::getIdByEmail($email);

		// encrypt password
		$encryptedPassword = FrontendProfilesModel::getEncryptedString($password, FrontendProfilesModel::getSetting($profileId, 'salt'));

		// get the status
		$loginStatus = FrontendModel::getContainer()->get('database')->getVar(
			'SELECT p.status
			 FROM profiles AS p
			 WHERE p.email = ? AND p.password = ?',
			array($email, $encryptedPassword)
		);

		return empty($loginStatus) ? self::LOGIN_INVALID : $loginStatus;
	}

	/**
	 * Get a profile object with information about a profile.
	 *
	 * @return FrontendProfilesProfile
	 */
	public static function getProfile()
	{
		return self::$profile;
	}

	/**
	 * Check if a profile is loggedin.
	 *
	 * @return bool
	 */
	public static function isLoggedIn()
	{
		// profile object exist? (this means the session/cookie checks have already happened in the current request and we cached the profile)
		if(isset(self::$profile)) return true;

		// check session
		elseif(SpoonSession::exists('frontend_profile_logged_in') && SpoonSession::get('frontend_profile_logged_in') === true)
		{
			// get session id
			$sessionId = SpoonSession::getSessionId();

			// get profile id
			$profileId = (int) FrontendModel::getContainer()->get('database')->getVar(
				'SELECT p.id
				 FROM profiles AS p
				 INNER JOIN profiles_sessions AS ps ON ps.profile_id = p.id
				 WHERE ps.session_id = ?',
				(string) $sessionId
			);

			// valid profile id
			if($profileId !== 0)
			{
				// update session date
				FrontendModel::getContainer()->get('database')->update('profiles_sessions', array('date' => FrontendModel::getUTCDate()), 'session_id = ?', $sessionId);

				// new user object
				self::$profile = new FrontendProfilesProfile($profileId);

				// logged in
				return true;
			}

			// invalid session
			else SpoonSession::set('frontend_profile_logged_in', false);
		}

		// check cookie
		elseif(CommonCookie::exists('frontend_profile_secret_key') && CommonCookie::get('frontend_profile_secret_key') != '')
		{
			// secret
			$secret = (string) CommonCookie::get('frontend_profile_secret_key');

			// get profile id
			$profileId = (int) FrontendModel::getContainer()->get('database')->getVar(
				'SELECT p.id
				 FROM profiles AS p
				 INNER JOIN profiles_sessions AS ps ON ps.profile_id = p.id
				 WHERE ps.secret_key = ?',
				$secret
			);

			// valid profile id
			if($profileId !== 0)
			{
				// get new secret key
				$profileSecret = FrontendProfilesModel::getEncryptedString(SpoonSession::getSessionId(), FrontendProfilesModel::getRandomString());

				// update session record
				FrontendModel::getContainer()->get('database')->update(
					'profiles_sessions',
					array(
						'session_id' => SpoonSession::getSessionId(),
						'secret_key' => $profileSecret,
						'date' => FrontendModel::getUTCDate()
					),
					'secret_key = ?',
					$secret
				);

				// set new cookie
				CommonCookie::set('frontend_profile_secret_key', $profileSecret);

				// set is_logged_in to true
				SpoonSession::set('frontend_profile_logged_in', true);

				// update last login
				FrontendProfilesModel::update($profileId, array('last_login' => FrontendModel::getUTCDate()));

				// new user object
				self::$profile = new FrontendProfilesProfile($profileId);

				// logged in
				return true;
			}

			// invalid cookie
			else CommonCookie::delete('frontend_profile_secret_key');
		}

		// no one is logged in
		return false;
	}

	/**
	 * Login a profile.
	 *
	 * @param int $profileId Login the profile with this id in.
	 * @param bool[optional] $remember Should we set a cookie for later?
	 * @return bool
	 */
	public static function login($profileId, $remember = false)
	{
		// redefine vars
		$profileId = (int) $profileId;
		$remember = (bool) $remember;
		$secretKey = null;

		// cleanup old sessions
		self::cleanupOldSessions();

		// set profile_logged_in to true
		SpoonSession::set('frontend_profile_logged_in', true);

		// should we remember the user?
		if($remember)
		{
			// generate secret key
			$secretKey = FrontendProfilesModel::getEncryptedString(SpoonSession::getSessionId(), FrontendProfilesModel::getRandomString());

			// set cookie
			CommonCookie::set('frontend_profile_secret_key', $secretKey);
		}

		// delete all records for this session to prevent duplicate keys (this should never happen)
		FrontendModel::getContainer()->get('database')->delete('profiles_sessions', 'session_id = ?', SpoonSession::getSessionId());

		// insert new session record
		FrontendModel::getContainer()->get('database')->insert(
			'profiles_sessions',
			array(
				'profile_id' => $profileId,
				'session_id' => SpoonSession::getSessionId(),
				'secret_key' => $secretKey,
				'date' => FrontendModel::getUTCDate()
			)
		);

		// update last login
		FrontendProfilesModel::update($profileId, array('last_login' => FrontendModel::getUTCDate()));

		// load the profile object
		self::$profile = new FrontendProfilesProfile($profileId);
	}

	/**
	 * Logout a profile.
	 */
	public static function logout()
	{
		// delete session records
		FrontendModel::getContainer()->get('database')->delete('profiles_sessions', 'session_id = ?', array(SpoonSession::getSessionId()));

		// set is_logged_in to false
		SpoonSession::set('frontend_profile_logged_in', false);

		// delete cookie
		CommonCookie::delete('frontend_profile_secret_key');
	}

	/**
	 * Update profile password and salt.
	 *
	 * @param int $profileId Profile id for which we are changing the password.
	 * @param string $password New password.
	 */
	public static function updatePassword($profileId, $password)
	{
		$profileId = (int) $profileId;
		$password = (string) $password;

		// get new salt
		$salt = FrontendProfilesModel::getRandomString();

		// encrypt password
		$encryptedPassword = FrontendProfilesModel::getEncryptedString($password, $salt);

		// update salt
		FrontendProfilesModel::setSetting($profileId, 'salt', $salt);

		// update password
		FrontendProfilesModel::update($profileId, array('password' => $encryptedPassword));
	}
}
