<?php

namespace Common\Core;

use ForkCMS\App\BaseModel;
use ForkCMS\Utility\Akismet;
use ForkCMS\Utility\Thumbnails;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * This class will initiate the frontend-application
 */
class Model extends BaseModel
{
    const HOME_PAGE_ID = 1;
    const ERROR_PAGE_ID = 404;

    /**
     * Cached modules
     *
     * @var array
     */
    protected static $modules = [];

    /**
     * Add a number to the string
     *
     * @param string $string The string where the number will be appended to.
     *
     * @return string
     */
    public static function addNumber(string $string): string
    {
        // split
        $chunks = explode('-', $string);

        // count the chunks
        $count = count($chunks);

        // get last chunk
        $last = $chunks[$count - 1];

        // is numeric
        if (!\SpoonFilter::isNumeric($last)) {
            // not numeric, so add -2
            return $string . '-2';
        }

        // remove last chunk
        array_pop($chunks);

        // join together, and increment the last one
        return implode('-', $chunks) . '-' . ((int) $last + 1);
    }

    /**
     * Generate a totally random but readable/speakable password
     *
     * @param int $length The maximum length for the password to generate.
     * @param bool $uppercaseAllowed Are uppercase letters allowed?
     * @param bool $lowercaseAllowed Are lowercase letters allowed?
     *
     * @return string
     */
    public static function generatePassword(
        int $length = 6,
        bool $uppercaseAllowed = true,
        bool $lowercaseAllowed = true
    ): string {
        // list of allowed vowels and vowel sounds
        $vowels = ['a', 'e', 'i', 'u', 'ae', 'ea'];

        // list of allowed consonants and consonant sounds
        $consonants = [
            'b',
            'c',
            'd',
            'g',
            'h',
            'j',
            'k',
            'm',
            'n',
            'p',
            'r',
            's',
            't',
            'u',
            'v',
            'w',
            'tr',
            'cr',
            'fr',
            'dr',
            'wr',
            'pr',
            'th',
            'ch',
            'ph',
            'st',
        ];

        $consonantsCount = count($consonants);
        $vowelsCount = count($vowels);
        $pass = '';
        $tmp = '';

        // create temporary pass
        for ($i = 0; $i < $length; ++$i) {
            $tmp .= ($consonants[random_int(0, $consonantsCount - 1)] .
                     $vowels[random_int(0, $vowelsCount - 1)]);
        }

        // reformat the pass
        for ($i = 0; $i < $length; ++$i) {
            if (random_int(0, 1) === 1) {
                $pass .= mb_strtoupper($tmp[$i]);

                continue;
            }

            $pass .= $tmp[$i];
        }

        // reformat it again, if uppercase isn't allowed
        if (!$uppercaseAllowed) {
            $pass = mb_strtolower($pass);
        }

        // reformat it again, if uppercase isn't allowed
        if (!$lowercaseAllowed) {
            $pass = mb_strtoupper($pass);
        }

        // return pass
        return $pass;
    }

    /**
     * Generate thumbnails based on the folders in the path
     * Use
     *  - 128x128 as foldername to generate an image where the width will be
     *      128px and the height will be 128px
     *  - 128x as foldername to generate an image where the width will be
     *      128px, the height will be calculated based on the aspect ratio.
     *  - x128 as foldername to generate an image where the height will be
     *      128px, the width will be calculated based on the aspect ratio.
     *
     * @param string $path The path wherein the thumbnail-folders will be stored.
     * @param string $sourceFile The location of the source file
     *
     * @deprecated Please use the service `ForkCMS\Utility\Thumbnails` instead.
     */
    public static function generateThumbnails(string $path, string $sourceFile): void
    {
        self::get(Thumbnails::class)->generate($path, $sourceFile);
    }

    /**
     * Delete thumbnails based on the folders in the path
     *
     * @param string $path The path wherein the thumbnail-folders exist.
     * @param string|null $thumbnail The filename to be deleted.
     *
     * @deprecated Please use the service `ForkCMS\Utility\Thumbnails` instead.
     */
    public static function deleteThumbnails(string $path, ?string $thumbnail): void
    {
        self::get(Thumbnails::class)->delete($path, $thumbnail);
    }

    /**
     * Get the thumbnail folders
     *
     * @param string $path The path
     * @param bool $includeSource Should the source-folder be included in the return-array.
     *
     * @return array
     *
     * @deprecated Please use the service `ForkCMS\Utility\Thumbnails` instead.
     */
    public static function getThumbnailFolders(string $path, bool $includeSource = false): array
    {
        return self::get(Thumbnails::class)->getFolders($path, $includeSource);
    }

    /**
     * Get the UTC date in a specific format. Use this method when inserting dates in the database!
     *
     * @param string $format The format to return the timestamp in. Default is MySQL datetime format.
     * @param int $timestamp The timestamp to use, if not provided the current time will be used.
     *
     * @return string
     */
    public static function getUTCDate(string $format = null, int $timestamp = null): string
    {
        $format = ($format !== null) ? (string) $format : 'Y-m-d H:i:s';
        if ($timestamp === null) {
            return gmdate($format);
        }

        return gmdate($format, $timestamp);
    }

    /**
     * Get the UTC timestamp for a date/time object combination.
     *
     * @param \SpoonFormDate $date An instance of \SpoonFormDate.
     * @param \SpoonFormTime $time An instance of \SpoonFormTime.
     *
     * @throws \Exception If provided $date, $time or both are invalid
     *
     * @return int
     */
    public static function getUTCTimestamp(\SpoonFormDate $date, \SpoonFormTime $time = null): int
    {
        // validate date/time object
        if (!$date->isValid() || ($time !== null && !$time->isValid())
        ) {
            throw new \Exception('You need to provide two objects that actually contain valid data.');
        }

        $year = gmdate('Y', $date->getTimestamp());
        $month = gmdate('m', $date->getTimestamp());
        $day = gmdate('j', $date->getTimestamp());
        $hour = 0;
        $minute = 0;

        if ($time !== null) {
            // define hour & minute
            list($hour, $minute) = explode(':', $time->getValue());
        }

        // make and return timestamp
        return mktime($hour, $minute, 0, $month, $day, $year);
    }

    public static function getModules(): array
    {
        // validate cache
        if (!empty(self::$modules)) {
            return self::$modules;
        }

        // get all modules
        $modules = (array) self::getContainer()->get('database')->getColumn('SELECT m.name FROM modules AS m');

        // add modules to the cache
        foreach ($modules as $module) {
            self::$modules[] = $module;
        }

        return self::$modules;
    }

    public static function getRequest(): Request
    {
        if (!self::requestIsAvailable()) {
            throw new RuntimeException('No request available');
        }

        return self::getContainer()->get('request_stack')->getCurrentRequest();
    }

    public static function requestIsAvailable(): bool
    {
        return self::getContainer()->has('request_stack')
               && self::getContainer()->get('request_stack')->getCurrentRequest() !== null;
    }

    protected static function getAkismet(): Akismet
    {
        $akismetKey = self::get('fork.settings')->get('Core', 'akismet_key');

        // invalid key, so we can't detect spam
        if (empty($akismetKey)) {
            throw new InvalidArgumentException('no akismet key found');
        }

        $akismet = new Akismet($akismetKey, SITE_URL);
        $akismet->setTimeOut(10);
        $akismet->setUserAgent('Fork CMS/' . FORK_VERSION);

        return $akismet;
    }

    public static function getSession(): SessionInterface
    {
        if (!self::requestIsAvailable()) {
            return self::getMockSession();
        }

        $request = self::getRequest();
        if ($request->hasSession()) {
            return $request->getSession();
        }

        $session = new Session();
        $session->start();
        $request->setSession($session);

        return $session;
    }

    /**
     * This method will return a mock session that will be only available while the code is running.
     * It is used for instance in the console
     *
     * @deprecated This has been added to fix an issue but should be fixed properly in fork 6.0.0
     *
     * @return Session
     */
    private static function getMockSession(): Session
    {
        if (!self::getContainer()->has('fork.mock.session')) {
            self::getContainer()->set('fork.mock.session', new Session(new MockArraySessionStorage()));
        }

        return self::get('fork.mock.session');
    }

    /**
     * This method returns the filesize in a human readable format according to the value
     *
     * @param int $fileSize
     * @return string
     */
    public static function prettyPrintFileSize(int $fileSize): string
    {
        if ($fileSize > 999999999) {
            return number_format($fileSize / 1000000000, 2, ',', ' ') . ' GB';
        }

        if ($fileSize > 999999) {
            return number_format($fileSize / 1000000, 2, ',', ' ') . ' MB';
        }

        if ($fileSize > 999) {
            return number_format($fileSize / 1000, 2, ',', ' ') . ' KB';
        }

        return $fileSize . ' bytes';
    }

    public static function recursiveHtmlspecialchars(array $data): array
    {
        array_walk_recursive(
            $data,
            static function (&$value) {
                if (is_string($value)) {
                    $value = htmlspecialchars($value);
                }
            }
        );

        return $data;
    }
}
