<?php

namespace Frontend\Core\Engine;

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
class Theme
{
    /**
     * The current active theme's name
     *
     * @var    string
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
        if (Model::getModuleSetting('Core', 'theme', 'core') != 'core') {
            // theme not yet specified
            if (strpos($file, 'src/Frontend/Themes/' . $theme) === false) {
                // add theme location
                $themeTemplate = str_replace(array('src/Frontend/'), array('src/Frontend/Themes/' . $theme . '/'), $file);

                // check if this template exists
                if (is_file(PATH_WWW . str_replace(PATH_WWW, '', $themeTemplate))) {
                    $file = $themeTemplate;
                }
            }
        }

        // check if the file exists
        if (!is_file(PATH_WWW . str_replace(PATH_WWW, '', $file))) {
            throw new Exception('The template (' . $file . ') does not exists.');
        }

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
        if (!self::$theme) {
            self::$theme = Model::getModuleSetting('Core', 'theme', null);
        }

        // return theme name
        return self::$theme;
    }
}
