<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will take care of functionality pertaining themes.
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class FrontendTheme
{
	/**
	 * The current active theme's name
	 *
	 * @var	string
	 */
	private static $theme;

	/**
	 * Get the file path based on the theme.
	 * If it does not exist in the theme it will return $file.
	 *
	 * @param string $file Path to the file.
	 * @return string Path to the (theme) file.
	 */
	public static function getPath($file)
	{
		// redefine
		$file = (string) $file;

		// theme name
		$theme = self::getTheme();

		// theme in use
		if(FrontendModel::getModuleSetting('core', 'theme', 'core') != 'core')
		{
			// theme not yet specified
			if(strpos($file, 'frontend/themes/' . $theme) === false)
			{
				// add theme location
				$themeTemplate = str_replace(array('frontend/'), array('frontend/themes/' . $theme . '/'), $file);

				// check if this template exists
				if(SpoonFile::exists(PATH_WWW . str_replace(PATH_WWW, '', $themeTemplate))) $file = $themeTemplate;
			}
		}

		// check if the file exists
		if(!SpoonFile::exists(PATH_WWW . str_replace(PATH_WWW, '', $file))) throw new FrontendException('The template (' . $file . ') doesn\'t exists.');

		// return template path
		return $file;
	}

	/**
	 * Gets the active theme name
	 *
	 * @return string
	 */
	public static function getTheme()
	{
		// theme name has not yet been saved, fetch and save it
		if(!self::$theme) self::$theme = FrontendModel::getModuleSetting('core', 'theme', null);

		// return theme name
		return self::$theme;
	}
}
