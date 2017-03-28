<?php
namespace Dropbox;

class Util
{
    const SPECIAL_ESCAPE_IN  = "\r\n\t\\\"";
    const SPECIAL_ESCAPE_OUT = "rnt\\\"";

    /**
     * Return a double-quoted version of the given string, using PHP-escape sequences
     * for all non-printable and non-ASCII characters.
     *
     * @param string $string
     *
     * @return string
     */
    public static function q($string)
    {
        # HACK: "self::SPECIAL_ESCAPE_OUT[...]" is not valid syntax in PHP 5.3, so put
        # it in a local variable first.
        $special_escape_out = self::SPECIAL_ESCAPE_OUT;

        $r = "\"";
        $len = \strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $c = $string[$i];
            $escape_i = \strpos(self::SPECIAL_ESCAPE_IN, $c);
            if ($escape_i !== false) {
                // Characters with a special escape code.
                $r .= "\\";
                $r .= $special_escape_out[$escape_i];
            }
            else if ($c >= "\x20" and $c <= "\x7e") {
                // Printable characters.
                $r .= $c;
            }
            else {
                // Generic hex escape code.
                $r .= "\\x";
                $r .= \bin2hex($c);
            }
        }
        $r .= "\"";
        return $r;
    }

    /**
     * If the given string begins with the UTF-8 BOM (byte order mark), remove it and
     * return whatever is left.  Otherwise, return the original string untouched.
     *
     * Though it's not recommended for UTF-8 to have a BOM, the standard allows it to
     * support software that isn't Unicode-aware.
     *
     * @param string $string
     *    A UTF-8 encoded string.
     *
     * @return string
     */
    public static function stripUtf8Bom($string)
    {
        if (strlen($string) == 0) return $string;

        if (\substr_compare($string, "\xEF\xBB\xBF", 0, 3) === 0) {
            $string = \substr($string, 3);
        }
        return $string;
    }

    /**
     * Return whether `$s` starts with `$prefix`.
     *
     * @param string $s
     * @param string $prefix
     * @param bool $caseInsensitive
     *
     * @return bool
     */
    public static function startsWith($s, $prefix, $caseInsensitive = false)
    {
        // substr_compare errors if $main_str is zero-length, so handle that
        // case specially here.
        if (\strlen($s) == 0) {
            return strlen($prefix) == 0;
        }

        return \substr_compare($s, $prefix, 0, strlen($prefix), $caseInsensitive) == 0;
    }

    /**
     * If `$s` starts with `$prefix`, return `$s` with `$prefix` removed.  Otherwise,
     * return `null`.
     *
     * @param string $s
     * @param string $prefix
     * @param bool $caseInsensitive
     *
     * @return string|null
     */
    public static function stripPrefix($s, $prefix, $caseInsensitive = false)
    {
        // substr_compare errors if $main_str is zero-length, so handle that
        // case specially here.
        if (strlen($s) == 0) {
            if (strlen($prefix) == 0) {
                return $s;
            } else {
                return null;
            }
        }

        $prefix_length = strlen($prefix);
        if (\substr_compare($s, $prefix, 0, strlen($prefix), $caseInsensitive) == 0) {
            return substr($s, $prefix_length);
        }
    }
}
