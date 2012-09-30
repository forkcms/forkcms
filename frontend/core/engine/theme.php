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
	* Gets info on the device that is using being used to view the site
	*
	* @return array
	*/
	public static function detectDevice() 
	{
		require_once (PATH_LIBRARY . '/external/Mobile_Detect.php');
		$detect = new Mobile_Detect();
		$device = array();

		if($detect->isMobile())
		{
			$device['mobile'] = array();
			
			if($detect->isiOS())
			{
				$device['mobile']['ios'] = true;
			}

			if($detect->isAndroidOS())
			{
				$device['mobile']['android'] = true;
			}
		}

		if($detect->isTablet())
		{
			$device['tablet'] = array();

			if($detect->isiOS())
			{
				$device['tablet']['ios'] = true;
			}

			if($detect->isAndroidOS())
			{
				$device['tablet']['android'] = true;
			}
		}

		if(empty($device))
		{
			$device['desktop'] = true;
		}

		return $device;
	}

	/**
	* Get file path for desktop theme
	*
	* @param string $file Path to file.
	* @param string $theme Current theme.
	* @return string
	*/
	public static function getDesktopPath($file, $theme)
	{
		return str_replace(array('frontend/'), array('frontend/themes/' . $theme . '/'), $file);
	}

	/**
	* Get file path for mobile theme.
	* If there is no mobile theme present it will get the desktop theme.
	*
	* @param string $file Path to file.
	* @param string $theme Current theme.
	* @param string $os Device OS.
	* @return string
	*/
	public static function getMobilePath($file, $theme, $os)
	{
		$path = dirname($file) . '/';
		$iospath = str_replace(array('frontend/', 'core'), array('frontend/themes/' . $theme . '/', 'mobile/ios'), $path);
		$andriodpath = str_replace(array('frontend/', 'core'), array('frontend/themes/' . $theme . '/', 'mobile/android'), $path);
		$mobilepath = str_replace(array('frontend/', 'core'), array('frontend/themes/' . $theme . '/', 'mobile'), $path);
		$mobilefile = basename($file);

		if($os == 'ios' && SpoonFile::exists(PATH_WWW . str_replace(PATH_WWW, '', $iospath) . $mobilefile))
		{
			return $iospath . $mobilefile;
		}
		elseif($os == 'android' && SpoonFile::exists(PATH_WWW . str_replace(PATH_WWW, '', $androidpath) . $mobilefile))
		{
			return $androidpath . $mobilefile;
		}
		elseif(SpoonFile::exists(PATH_WWW . str_replace(PATH_WWW, '', $mobilepath) . $mobilefile))
		{
			return $mobilepath . $mobilefile;
		}
		else
		{
			return self::getDesktopPath($file, $theme);
		}
	}

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

		// users device
		$device = self::detectDevice();

		// theme in use
		if(FrontendModel::getModuleSetting('core', 'theme', 'core') != 'core')
		{
			// theme not yet specified
			if(strpos($file, 'frontend/themes/' . $theme) === false)
			{
				// get theme location based on users device
				if(!empty($device['mobile']))
				{
					if(!empty($device['mobile']['ios']))
					{
						$themeTemplate = self::getMobilePath($file, $theme, 'ios');
					}
					else
					{
						$themeTenplate = self::getMobilePath($file, $theme, 'android');
					}
				}
				elseif(!empty($device['tablet']))
				{
					if(!empty($device['tablet']['ios']))
					{
						$themeTemplate = self::getMobilePath($file, $theme, 'ios');
					}
					else
					{
						$themeTenplate = self::getMobilePath($file, $theme, 'android');
					}
				}
				else
				{
					$themeTemplate = self::getDesktopPath($file, $theme);
				}

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
	* Get file path for tablet theme.
	* If there is no tablet theme present it will get the desktop theme.
	*
	* @param string $file Path to file.
	* @param string $theme Current theme.
	* @param string $os Device OS.
	* @return string
	*/
	public static function getTabletPath($file, $theme, $os)
	{
		$path = dirname($file) . '/';
		$iospath = str_replace(array('frontend/', 'core'), array('frontend/themes/' . $theme . '/', 'tablet/ios'), $path);
		$andriodpath = str_replace(array('frontend/', 'core'), array('frontend/themes/' . $theme . '/', 'tablet/android'), $path);
		$mobilepath = str_replace(array('frontend/', 'core'), array('frontend/themes/' . $theme . '/', 'tablet'), $path);
		$tabletfile = basename($file);

		if($os == 'ios' && SpoonFile::exists(PATH_WWW . str_replace(PATH_WWW, '', $iospath) . $tabletfile))
		{
			return $iospath . $tabletfile;
		}
		elseif($os == 'android' && SpoonFile::exists(PATH_WWW . str_replace(PATH_WWW, '', $androidpath) . $tabletfile))
		{
			return $androidpath . $tabletfile;
		}
		elseif(SpoonFile::exists(PATH_WWW . str_replace(PATH_WWW, '', $tabletpath) . $tabletfile))
		{
			return $tabletpath . $tabletfile;
		}
		else
		{
			return self::getDesktopPath($file, $theme);
		}
	}

	/**
	 * Gets the active theme name
	 *
	 * @return string
	 */
	public static function getTheme()
	{
		// theme nama has not yet been saved, fetch and save it
		if(!self::$theme) self::$theme = FrontendModel::getModuleSetting('core', 'theme', null);

		// return theme name
		return self::$theme;
	}
}
