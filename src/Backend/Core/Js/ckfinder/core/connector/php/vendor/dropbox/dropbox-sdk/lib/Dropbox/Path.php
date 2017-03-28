<?php
namespace Dropbox;

/**
 * Path validation functions.
 */
final class Path
{
    /**
     * Return whether the given path is a valid Dropbox path.
     *
     * @param string $path
     *    The path you want to check for validity.
     *
     * @return bool
     *    Whether the path was valid or not.
     */
    static function isValid($path)
    {
        $error = self::findError($path);
        return ($error === null);
    }

    /**
     * Return whether the given path is a valid non-root Dropbox path.
     * This is the same as {@link isValid} except `"/"` is not allowed.
     *
     * @param string $path
     *    The path you want to check for validity.
     *
     * @return bool
     *    Whether the path was valid or not.
     */
    static function isValidNonRoot($path)
    {
        $error = self::findErrorNonRoot($path);
        return ($error === null);
    }

    /**
     * If the given path is a valid Dropbox path, return `null`,
     * otherwise return an English string error message describing what is wrong with the path.
     *
     * @param string $path
     *    The path you want to check for validity.
     *
     * @return string|null
     *    If the path was valid, return `null`.  Otherwise, returns
     *    an English string describing the problem.
     */
    static function findError($path)
    {
        Checker::argStringNonEmpty("path", $path);

        $matchResult = preg_match('%^(?:
                  [\x09\x0A\x0D\x20-\x7E]            # ASCII
                | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
                | \xE0[\xA0-\xBF][\x80-\xBD]         # excluding overlongs, FFFE, and FFFF
                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
                | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
                )*$%xs', $path);

        if ($matchResult !== 1) {
            return "must be valid UTF-8; BMP only, no surrogates, no U+FFFE or U+FFFF";
        }

        if ($path[0] !== "/") return "must start with \"/\"";
        $l = strlen($path);
        if ($l === 1) return null;  // Special case for "/"

        if ($path[$l-1] === "/") return "must not end with \"/\"";

        // TODO: More checks.

        return null;
    }

    /**
     * If the given path is a valid non-root Dropbox path, return `null`,
     * otherwise return an English string error message describing what is wrong with the path.
     * This is the same as {@link findError} except `"/"` will yield an error message.
     *
     * @param string $path
     *    The path you want to check for validity.
     *
     * @return string|null
     *    If the path was valid, return `null`.  Otherwise, returns
     *    an English string describing the problem.
     */
    static function findErrorNonRoot($path)
    {
        if ($path == "/") return "root path not allowed";
        return self::findError($path);
    }

    /**
     * Return the last component of a path (the file or folder name).
     *
     * <code>
     * Path::getName("/Misc/Notes.txt") // "Notes.txt"
     * Path::getName("/Misc")           // "Misc"
     * Path::getName("/")               // null
     * </code>
     *
     * @param string $path
     *    The full path you want to get the last component of.
     *
     * @return null|string
     *    The last component of `$path` or `null` if the given
     *    `$path` was `"/"`.
     */
    static function getName($path)
    {
        Checker::argStringNonEmpty("path", $path);

        if ($path[0] !== "/") {
            throw new \InvalidArgumentException("'path' must start with \"/\"");
        }
        $l = strlen($path);
        if ($l === 1) return null;
        if ($path[$l-1] === "/") {
            throw new \InvalidArgumentException("'path' must not end with \"/\"");
        }

        $lastSlash = strrpos($path, "/");
        return substr($path, $lastSlash+1);
    }

    /**
     * @internal
     *
     * @param string $argName
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    static function checkArg($argName, $value)
    {
        Checker::argStringNonEmpty($argName, $value);

        $error = self::findError($value);
        if ($error !== null) throw new \InvalidArgumentException("'$argName': bad path: $error: ".Util::q($value));
    }

    /**
     * @internal
     *
     * @param string $argName
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    static function checkArgOrNull($argName, $value)
    {
        if ($value === null) return;
        self::checkArg($argName, $value);
    }

    /**
     * @internal
     *
     * @param string $argName
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    static function checkArgNonRoot($argName, $value)
    {
        Checker::argStringNonEmpty($argName, $value);

        $error = self::findErrorNonRoot($value);
        if ($error !== null) throw new \InvalidArgumentException("'$argName': bad path: $error: ".Util::q($value));
    }
}
