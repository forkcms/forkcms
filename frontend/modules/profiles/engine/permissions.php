<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using for checking the permissions
 * in the frontend.
 *
 * @author Lowie Benoot <lowie.benoot@netlash.com>
 */
class FrontendProfilesPermissions
{
	/**
	 * The permissions.
	 *
	 * @var array
	 */
	private static $permissions = array();

	/**
	 * The profile. If it is false, it means that the user isn't logged in.
	 *
	 * @var mixed
	 */
	private static $profile;

	/**
	 * Checks if the current user is allowed to view a certain item.
	 * An item is described by its module and id.
	 *
	 * @param string $module The module where the item belongs to.
	 * @param int $id The id where the item belongs to.
	 * @return bool
	 */
	public static function isAllowed($module, $id)
	{
		$module = (string) $module;
		$id = (int) $id;

		self::loadPermissionsForModule($module);
		self::loadProfile();

		// is the item secured?
		if(isset(self::$permissions[$module][$id]) && self::$permissions[$module][$id][0] == 'Y')
		{
			if(self::$profile)
			{
				// is the user in one of the allowed groups?
				foreach(self::$permissions[$module][$id] as $groupId => $allowed)
				{
					if($allowed == 'Y' && self::$profile->isInGroup($groupId)) return true;
				}
			}
		}

		// the item is not secured, so the user is allowed
		else
		{
			return true;
		}

		// if we make it to this point, the user is not allowed
		return false;
	}

	/**
	 * Load all the permissions for a certain module.
	 *
	 * @param string $module
	 */
	private static function loadPermissionsForModule($module)
	{
		if(!isset(self::$permissions[$module]))
		{
			$db = FrontendModel::getDB();

			// load permissions from database
			$permissions = (array) $db->getRecords(
				'SELECT i.*
				 FROM profiles_groups_permissions AS i
				 WHERE i.module = ?',
				array((string) $module)
			);

			// any permissions found?
			if(!empty($permissions))
			{
				// sort and group the permissions
				$sorted = array();
				foreach($permissions as $permission)
				{
					if(!isset($sorted[$permission['other_id']]))
					{
						$sorted[$permission['other_id']] = array();
					}

					$sorted[$permission['other_id']][$permission['group_id']] = $permission['allowed'];
				}

				self::$permissions[$module] = $sorted;
			}

			// no permissions found
			else
			{
				self::$permissions[$module] = array();
			}
		}

		return self::$permissions[$module];
	}

	/**
	 * Load the profile.
	 */
	private static function loadProfile()
	{
		// load the profile if it isn't loaded yet?
		if(self::$profile === null)
		{
			if(FrontendProfilesAuthentication::isLoggedIn())
			{
				// get the userprofile
				self::$profile = FrontendProfilesAuthentication::getProfile();
			}

			else
			{
				self::$profile = false;
			}

			// set the profile to false if it is null. This means the user isn't logged in.
			if(self::$profile === null) self::$profile = false;
		}
	}
}