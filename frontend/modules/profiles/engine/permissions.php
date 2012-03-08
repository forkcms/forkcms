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
	 * Is the profiles module installed?
	 *
	 * @var bool
	 */
	private static $isInstalled;

	/**
	 * The permissions.
	 *
	 * @var array
	 */
	private static $permissions = array();

	/**
	 * The profile objet. If it is false, it means that the user isn't logged in.
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
	 * @param bool[optional] $checkNavigation Should we also check if the user is allowed to see the item in the navigation?
	 * @param bool[optional] $redirect Should we redirect the user if he's not allowed?
	 * @return bool
	 */
	public static function isAllowed($module, $id, $checkNavigation = false, $redirect = false)
	{
		// if the profiles module is not installed, the user is certainly allowed
		if(!self::isInstalled())
		{
			return true;
		}

		$module = (string) $module;
		$id = (int) $id;

		self::loadPermissionsForModule($module);
		self::loadProfile();

		// is the item secured?
		if(isset(self::$permissions[$module][$id]) && self::$permissions[$module][$id]['is_secured'])
		{
			// user is logged in?
			if(self::$profile)
			{
				// is the user in one of the allowed groups?
				foreach(self::$permissions[$module][$id]['groups'] as $group)
				{
					if(self::$profile->isInGroup($group)) return true;
				}
			}

			// the user is now allowded, should we check if the user allowed to see the item in the navigation?
			if($checkNavigation)
			{
				return self::$permissions[$module][$id]['show_in_navigation'];
			}
		}

		// the item is not secured, so the user is allowed
		else
		{
			return true;
		}

		// if we make it to this point, the user is not allowed
		if($redirect)
		{
			// user is logged in? Redirect to the 'forbidden' page
			if(self::$profile)
			{
				SpoonHTTP::redirect(FrontendNavigation::getURL(403));
			}

			// not logged in, redirect to the login page
			else
			{
				$url = Spoon::get('url');
				$queryString = urlencode('/' . $url->getQueryString());

				SpoonHTTP::redirect(
					FrontendNavigation::getURLForBlock('profiles', 'login') . '?queryString=' . $queryString
				);
			}
		}

		return false;
	}

	/**
	 * Checks if the profiles module is installed
	 *
	 * @return bool
	 */
	private static function isInstalled()
	{
		if(!self::$isInstalled)
		{
			self::$isInstalled = in_array('profiles', FrontendModel::getModules());
		}

		return self::$isInstalled;
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
				'SELECT i.other_id, i.data
				 FROM profiles_groups_permissions AS i
				 WHERE i.module = ?',
				array((string) $module),
				'other_id'
			);

			// any permissions found?
			if(!empty($permissions))
			{
				// create an array with the other id as key and the data as values
				$sorted = array();
				foreach($permissions as $otherId => $permission)
				{
					// unserialize the data
					$sorted[$otherId] = unserialize($permission['data']);
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