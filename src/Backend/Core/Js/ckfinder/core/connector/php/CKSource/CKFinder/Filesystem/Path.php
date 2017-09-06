<?php

/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2016, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

namespace CKSource\CKFinder\Filesystem;

/**
 * The Path class.
 */
class Path
{
    const REGEX_INVALID_PATH = ',(/\.)|[[:cntrl:]]|(//)|(\\\\)|([:\*\?\"\<\>\|]),';

    /**
     * Checks if a given path is valid.
     *
     * @param string $path path to be validated
     *
     * @return bool true if the path is valid.
     */
    public static function isValid($path)
    {
        return !preg_match(static::REGEX_INVALID_PATH, $path);
    }

    /**
     * Normalizes the path so it starts and ends end with a "/".
     *
     * @param string $path input path
     *
     * @return string normalized path
     */
    public static function normalize($path)
    {
        if (!strlen($path)) {
            $path = '/';
        } elseif ($path !== '/') {
            $path = '/' . trim($path, '/') . '/';
        }

        return $path;
    }

    /**
     * This function behaves similarly to `System.IO.Path.Combine` in C#, the only difference is that it also
     * accepts null values and treats them as an empty string.
     *
     * @param string [$arg1, $arg2, ...]
     *
     * @return string
     */
    public static function combine()
    {
        $args = func_get_args();

        if (!count($args)) {
            return null;
        }

        $result = array_shift($args);

        $isDirSeparator = function ($char) {
            return $char === '/' || $char === '\\';
        };

        $argsCount = count($args);

        for ($i = 0; $i < $argsCount; $i++) {
            $path1 = $result;
            $path2 = $args[$i];

            if (null === $path1) {
                $path1 = '';
            }

            if (null === $path2) {
                $path2 = '';
            }

            if (!strlen($path2)) {
                if (strlen($path1)) {
                    $_lastCharP1 = substr($path1, -1, 1);
                    if (!$isDirSeparator($_lastCharP1)) {
                        $path1 .= '/';
                    }
                }
            } else {
                $_firstCharP2 = substr($path2, 0, 1);
                if (strlen($path1)) {
                    if (strpos($path2, $path1) === 0) {
                        $result = $path2;
                        continue;
                    }
                    $_lastCharP1 = substr($path1, -1, 1);
                    if (!$isDirSeparator($_lastCharP1) && !$isDirSeparator($_firstCharP2)) {
                        $path1 .= '/';
                    } elseif ($isDirSeparator($_lastCharP1) && $isDirSeparator($_firstCharP2)) {
                        $path2 = substr($path2, 1);
                    }
                } else {
                    $result = $path2;
                    continue;
                }
            }

            $result = $path1 . $path2;
        }


        return $result;
    }
}
