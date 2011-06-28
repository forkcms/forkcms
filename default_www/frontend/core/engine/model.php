<?php

/**
 * In this file we store all generic functions that we will be using in the frontend.
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendModel
{
	/**
	 * cached module-settings
	 *
	 * @var	array
	 */
	private static $moduleSettings = array();


	/**
	 * Add a number to the string
	 *
	 * @return	string
	 * @param	string $string	The string where the number will be appended to.
	 */
	public static function addNumber($string)
	{
		// split
		$chunks = explode('-', $string);

		// count the chunks
		$count = count($chunks);

		// get last chunk
		$last = $chunks[$count - 1];

		// is nummeric
		if(SpoonFilter::isNumeric($last))
		{
			// remove last chunk
			array_pop($chunks);

			// join together, and increment the last one
			$string = implode('-', $chunks ) . '-' . ((int) $last + 1);
		}

		// not numeric, so add -2
		else $string .= '-2';

		// return
		return $string;
	}


	/**
	 * Add parameters to an URL
	 *
	 * @return	string
	 * @param	string $URL			The URL to append the parameters too.
	 * @param	array $parameters	The parameters as key-value-pairs.
	 */
	public static function addURLParameters($URL, array $parameters)
	{
		// redefine
		$URL = (string) $URL;

		// no parameters means no appending
		if(empty($parameters)) return $URL;

		// split to remove the hash
		$chunks = explode('#', $URL, 2);

		// init var
		$hash = '';

		if(isset($chunks[1]))
		{
			// reset URL
			$URL = $chunks[0];

			// store has
			$hash = '#' . $chunks[1];
		}

		// build querystring
		$queryString = http_build_query($parameters, null, '&amp;');

		// already GET parameters?
		if(mb_strpos($URL, '?') !== false) return $URL .= '&' . $queryString . $hash;

		// no GET-parameters defined before
		else return $URL .= '?' . $queryString . $hash;
	}


	/**
	 * Generate a totally random but readable/speakable password
	 *
	 * @return	string
	 * @param	int[optional] $length				The maximum length for the password to generate.
	 * @param	bool[optional] $uppercaseAllowed	Are uppercase letters allowed?
	 * @param	bool[optional] $lowercaseAllowed	Are lowercase letters allowed?
	 */
	public static function generatePassword($length = 6, $uppercaseAllowed = true, $lowercaseAllowed = true)
	{
		// list of allowed vowels and vowelsounds
		$vowels = array('a', 'e', 'i', 'u', 'ae', 'ea');

		// list of allowed consonants and consonant sounds
		$consonants = array('b', 'c', 'd', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'r', 's', 't', 'u', 'v', 'w', 'tr', 'cr', 'fr', 'dr', 'wr', 'pr', 'th', 'ch', 'ph', 'st');

		// init vars
		$consonantsCount = count($consonants);
		$vowelsCount = count($vowels);
		$pass = '';
		$tmp = '';

		// create temporary pass
		for($i = 0; $i < $length; $i++) $tmp .= ($consonants[rand(0, $consonantsCount - 1)] . $vowels[rand(0, $vowelsCount - 1)]);

		// reformat the pass
		for($i = 0; $i < $length; $i++)
		{
			if(rand(0, 1) == 1) $pass .= strtoupper(substr($tmp, $i, 1));
			else $pass .= substr($tmp, $i, 1);
		}

		// reformat it again, if uppercase isn't allowed
		if(!$uppercaseAllowed) $pass = strtolower($pass);

		// reformat it again, if uppercase isn't allowed
		if(!$lowercaseAllowed) $pass = strtoupper($pass);

		// return pass
		return $pass;
	}


	/**
	 * Get (or create and get) a database-connection
	 * @later split the write and read connection
	 *
	 * @return	SpoonDatabase
	 * @param	bool[optional] $write	Do you want the write-connection or not?
	 */
	public static function getDB($write = false)
	{
		// redefine
		$write = (bool) $write;

		// do we have a db-object ready?
		if(!Spoon::exists('database'))
		{
			// create instance
			$db = new SpoonDatabase(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

			// utf8 compliance & MySQL-timezone
			$db->execute('SET CHARACTER SET utf8, NAMES utf8, time_zone = "+0:00"');

			// store
			Spoon::set('database', $db);
		}

		// return db-object
		return Spoon::get('database');
	}


	/**
	 * Get a module setting
	 *
	 * @return	mixed
	 * @param	string $module					The module wherefor a setting has to be retrieved.
	 * @param	string $name					The name of the setting to be retrieved.
	 * @param	mixed[optional] $defaultValue	A value that will be stored if the setting isn't present.
	 */
	public static function getModuleSetting($module, $name, $defaultValue = null)
	{
		// redefine
		$module = (string) $module;
		$name = (string) $name;

		// get them all
		if(empty(self::$moduleSettings))
		{
			// fetch settings
			$settings = (array) self::getDB()->getRecords('SELECT ms.module, ms.name, ms.value
															FROM modules_settings AS ms
															INNER JOIN modules AS m ON ms.module = m.name
															WHERE m.active = ?',
															array('Y'));

			// loop settings and cache them, also unserialize the values
			foreach($settings as $row) self::$moduleSettings[$row['module']][$row['name']] = unserialize($row['value']);
		}

		// if the setting doesn't exists, store it (it will be available from te cache)
		if(!array_key_exists($module, self::$moduleSettings) || !array_key_exists($name, self::$moduleSettings[$module])) self::setModuleSetting($module, $name, $defaultValue);

		// return
		return self::$moduleSettings[$module][$name];
	}


	/**
	 * Get all module settings at once
	 *
	 * @return	array
	 * @param	string $module	The module wherefor all settings has to be retrieved.
	 */
	public static function getModuleSettings($module)
	{
		// redefine
		$module = (string) $module;

		// get them all
		if(empty(self::$moduleSettings[$module]))
		{
			// fetch settings
			$settings = (array) self::getDB()->getRecords('SELECT ms.module, ms.name, ms.value
															FROM modules_settings AS ms');

			// loop settings and cache them, also unserialize the values
			foreach($settings as $row) self::$moduleSettings[$row['module']][$row['name']] = unserialize($row['value']);
		}

		// validate again
		if(!isset(self::$moduleSettings[$module])) return array();

		// return
		return self::$moduleSettings[$module];
	}


	/**
	 * Get all data for a page
	 *
	 * @return	array
	 * @param	int $pageId		The pageId wherefor the data will be retrieved.
	 */
	public static function getPage($pageId)
	{
		// redefine
		$pageId = (int) $pageId;

		// get database instance
		$db = self::getDB();

		// get data
		$record = (array) $db->getRecord('SELECT p.id, p.revision_id, p.template_id, p.title, p.navigation_title, p.navigation_title_overwrite, p.data,
												m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
												m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
												m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
												m.custom AS meta_custom,
												m.url, m.url_overwrite,
												t.path AS template_path, t.data AS template_data
											FROM pages AS p
											INNER JOIN meta AS m ON p.meta_id = m.id
											INNER JOIN pages_templates AS t ON p.template_id = t.id
											WHERE p.id = ? AND p.status = ? AND p.hidden = ? AND p.language = ?
											LIMIT 1',
											array($pageId, 'active', 'N', FRONTEND_LANGUAGE));

		// validate
		if(empty($record)) return array();

		// unserialize page data and template data
		if(isset($record['data']) && $record['data'] != '') $record['data'] = unserialize($record['data']);
		if(isset($record['template_data']) && $record['template_data'] != '') $record['template_data'] = @unserialize($record['template_data']);

		// determine amount of blocks needed
		$numBlocks = count($record['template_data']['names']);

		// get blocks
		$record['blocks'] = (array) $db->getRecords('SELECT pb.extra_id, pb.html,
														pe.module AS extra_module, pe.type AS extra_type, pe.action AS extra_action, pe.data AS extra_data
														FROM pages_blocks AS pb
														LEFT OUTER JOIN pages_extras AS pe ON pb.extra_id = pe.id
														WHERE pb.revision_id = ? AND pb.status = ?
														ORDER BY pb.id',
														array($record['revision_id'], 'active'));

		// remove redundant blocks
		$record['blocks'] = array_splice($record['blocks'], 0, $numBlocks);

		// loop blocks
		foreach($record['blocks'] as $index => $row)
		{
			// unserialize data if it is available
			if(isset($row['data'])) $record['blocks'][$index]['data'] = unserialize($row['data']);
		}

		// return page record
		return $record;
	}


	/**
	 * Get a revision for a page
	 *
	 * @return	array
	 * @param	int $revisionId		The revisionID.
	 */
	public static function getPageRevision($revisionId)
	{
		// redefine
		$revisionId = (int) $revisionId;

		// get database instance
		$db = self::getDB();

		// get data
		$record = (array) $db->getRecord('SELECT p.id, p.revision_id, p.template_id, p.title, p.navigation_title, p.navigation_title_overwrite, p.data,
												m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
												m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
												m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
												m.custom AS meta_custom,
												m.url, m.url_overwrite,
												t.path AS template_path, t.data AS template_data
											FROM pages AS p
											INNER JOIN meta AS m ON p.meta_id = m.id
											INNER JOIN pages_templates AS t ON p.template_id = t.id
											WHERE p.revision_id = ? AND p.language = ?
											LIMIT 1',
											array($revisionId, FRONTEND_LANGUAGE));

		// validate
		if(empty($record)) return array();

		// unserialize page data and template data
		if(isset($record['data']) && $record['data'] != '') $record['data'] = unserialize($record['data']);
		if(isset($record['template_data']) && $record['template_data'] != '') $record['template_data'] = @unserialize($record['template_data']);

		// get blocks
		$record['blocks'] = (array) $db->getRecords('SELECT pb.extra_id, pb.html,
														pe.module AS extra_module, pe.type AS extra_type, pe.action AS extra_action, pe.data AS extra_data
														FROM pages_blocks AS pb
														LEFT OUTER JOIN pages_extras AS pe ON pb.extra_id = pe.id
														WHERE pb.revision_id = ? AND pb.status = ?
														ORDER BY pb.id',
														array($record['revision_id'], 'active'));

		// loop blocks
		foreach($record['blocks'] as $index => $row)
		{
			// unserialize data if it is available
			if(isset($row['data'])) $record['blocks'][$index]['data'] = unserialize($row['data']);
		}

		// return page record
		return $record;
	}


	/**
	 * Get the UTC date in a specific format. Use this method when inserting dates in the database!
	 *
	 * @return	string
	 * @param	string[optional] $format	The format wherin the data will be returned, if not provided we will return it in MySQL-datetime-format.
	 * @param	int[optional] $timestamp	A UNIX-timestamp that will be used as base.
	 */
	public static function getUTCDate($format = null, $timestamp = null)
	{
		// init var
		$format = ($format !== null) ? (string) $format : 'Y-m-d H:i:s';

		// no timestamp given
		if($timestamp === null) return gmdate($format);

		// timestamp given
		return gmdate($format, (int) $timestamp);
	}


	/**
	 * General method to check if something is spam
	 *
	 * @return	bool
	 * @param	string $content				The content that was submitted.
	 * @param	string $permaLink			The permanent location of the entry the comment was submitted to.
	 * @param	string[optional] $author	Commenters name.
	 * @param	string[optional] $email		Commenters email address.
	 * @param	string[optional] $URL		Commenters URL.
	 * @param	string[optional] $type		May be blank, comment, trackback, pingback, or a made up value like "registration".
	 */
	public static function isSpam($content, $permaLink, $author = null, $email = null, $URL = null, $type = 'comment')
	{
		// get some settings
		$akismetKey = self::getModuleSetting('core', 'akismet_key');

		// invalid key, so we can't detect spam
		if($akismetKey === '') return false;

		// require the class
		require_once PATH_LIBRARY . '/external/akismet.php';

		// create new instance
		$akismet = new Akismet($akismetKey, SITE_URL);

		// set properties
		$akismet->setTimeOut(10);
		$akismet->setUserAgent('Fork CMS/2.1');

		// try it to decide if the item is spam
		try
		{
			// check with Akismet if the item is spam
			return $akismet->isSpam($content, $author, $email, $URL, $permaLink, $type);
		}

		// catch exceptions
		catch(Exception $e)
		{
			// in debug mode we want to see exceptions, otherwise the fallback will be triggered
			if(SPOON_DEBUG) throw $e;
		}

		// when everything fails
		return false;
	}


	/**
	 * Push a notification to Apple's notifications-server
	 *
	 * @return	void
	 * @param	mixed $alert						The message/dictonary to send.
	 * @param	int[optional] $badge				The number for the badge.
	 * @param	string[optional] $sound				The sound that should be played.
	 * @param 	array[optional] $extraDictionaries	Extra dictionaries.
	 */
	public static function pushToAppleApp($alert, $badge = null, $sound = null, array $extraDictionaries = null)
	{
		// get ForkAPI-keys
		$publicKey = FrontendModel::getModuleSetting('core', 'fork_api_public_key', '');
		$privateKey = FrontendModel::getModuleSetting('core', 'fork_api_private_key', '');

		// validate keys
		if($publicKey != '' && $privateKey != '')
		{
			// get all apple-device tokens
			$deviceTokens = (array) FrontendModel::getDB()->getColumn('SELECT s.value
																		FROM users AS i
																		INNER JOIN users_settings AS s
																		WHERE i.active = ? AND i.deleted = ? AND s.name = ? AND s.value != ?',
																		array('Y', 'N', 'apple_device_token', 'N;'));

			// any devices?
			if(!empty($deviceTokens))
			{
				// init var
				$tokens = array();

				// loop devices
				foreach($deviceTokens as $row)
				{
					// unserialize
					$row = unserialize($row);

					// loop and add
					foreach($row as $item) $tokens[] = $item;
				}

				// any tokens?
				if(!empty($tokens))
				{
					// require the class
					require_once PATH_LIBRARY . '/external/fork_api.php';

					// create instance
					$forkAPI = new ForkAPI($publicKey, $privateKey);

					try
					{
						// push
						$response = $forkAPI->applePush($tokens, $alert, $badge, $sound, $extraDictionaries);

						if(!empty($response))
						{
							// get db
							$db = FrontendModel::getDB(true);

							// loop the failed keys and remove them
							foreach($response as $deviceToken)
							{
								// get setting wherin the token is available
								$row = $db->getRecord('SELECT i.*
														FROM users_settings AS i
														WHERE i.name = ? AND i.value LIKE ?',
														array('apple_device_token', '%' . $deviceToken . '%'));

								// any rows?
								if(!empty($row))
								{
									// reset data
									$data = unserialize($row['value']);

									// loop keys
									foreach($data as $key => $token)
									{
										// match and unset if needed.
										if($token == $deviceToken) unset($data[$key]);
									}

									// no more tokens left?
									if(empty($data)) $db->delete('users_settings', 'user_id = ? AND name = ?', array($row['user_id'], $row['name']));

									// save
									else $db->update('users_settings', array('value' => serialize($data)), 'user_id = ? AND name = ?', array($row['user_id'], $row['name']));
								}
							}

						}
					}

					// catch exceptions
					catch(Exception $e)
					{
						if(SPOON_DEBUG) throw $e;
					}
				}
			}
		}

	}


	/**
	 * Store a modulesetting
	 *
	 * @return	void
	 * @param	string $module		The module wherefor a setting has to be stored.
	 * @param	string $name		The name of the setting.
	 * @param	mixed $value		The value (will be serialized so make sure the type is correct).
	 */
	public static function setModuleSetting($module, $name, $value)
	{
		// redefine
		$module = (string) $module;
		$name = (string) $name;
		$value = serialize($value);

		// store
		self::getDB(true)->execute('INSERT INTO modules_settings (module, name, value)
									VALUES (?, ?, ?)
									ON DUPLICATE KEY UPDATE value = ?',
									array($module, $name, $value, $value));

		// store in cache
		self::$moduleSettings[$module][$name] = unserialize($value);
	}
}

?>