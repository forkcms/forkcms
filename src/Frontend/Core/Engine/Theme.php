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
 */
class Theme
{
    /**
     * The current active theme's name
     *
     * @var string
     */
    private static $theme;

    /**
     * Get the file path based on the theme.
     * If it does not exist in the theme it will return $file.
     *
     * @param string $filePath Path to the file.
     *
     * @throws Exception
     *
     * @return string Path to the (theme) file.
     */
    public static function getPath(string $filePath): string
    {
        $filePath = self::getFilePathForcurrentTheme(self::getTheme(), $filePath);

        // check if the file exists
        if (!is_file(PATH_WWW . str_replace(PATH_WWW, '', $filePath))) {
            throw new Exception('The template (' . $filePath . ') does not exist.');
        }

        return $filePath;
    }

    private static function getFilePathForCurrentTheme(string $theme, string $filePath): string
    {
        // just return the file if the theme is already in the file path
        if (mb_strpos($filePath, 'src/Frontend/Themes/' . $theme) !== false) {
            return $filePath;
        }

        // add theme location
        $themeTemplate = str_replace('src/Frontend/', 'src/Frontend/Themes/' . $theme . '/', $filePath);

        // check if this template exists
        if (is_file(PATH_WWW . str_replace(PATH_WWW, '', $themeTemplate))) {
            return $themeTemplate;
        }

        return $filePath;
    }

    /**
     * Gets the active theme name
     *
     * @return string
     */
    public static function getTheme(): string
    {
        // theme name has not yet been saved, fetch and save it
        if (!self::$theme) {
            self::$theme = Model::get('fork.settings')->get('Core', 'theme', null);
        }

        // return theme name
        return self::$theme;
    }
}
