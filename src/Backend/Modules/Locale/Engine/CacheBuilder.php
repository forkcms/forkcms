<?php

namespace Backend\Modules\Locale\Engine;

use Symfony\Component\Filesystem\Filesystem;

/**
 * In this file, the locale cache is build
 */
class CacheBuilder
{
    /**
     * @var \SpoonDatabase
     */
    protected $database;

    /**
     * @var array
     */
    protected $locale;

    /**
     * @param \SpoonDatabase $database
     */
    public function __construct(\SpoonDatabase $database)
    {
        $this->database = $database;
    }

    /**
     * @param string $language
     * @param string $application Backend or Frontend
     */
    public function buildCache(string $language, string $application): void
    {
        $this->locale = $this->getLocale($language, $application);
        $this->dumpJsonCache($language, $application);
    }

    /**
     * Fetches locale for a certain language application combo
     *
     * @param string $language
     * @param string $application
     *
     * @return array
     */
    protected function getLocale(string $language, string $application): array
    {
        return (array) $this->database->getRecords(
            'SELECT type, module, name, value
             FROM locale
             WHERE language = ? AND application = ?
             ORDER BY type ASC, name ASC, module ASC',
            [$language, $application]
        );
    }

    /**
     * Builds the array that will be put in cache
     *
     * @param string $language
     * @param string $application
     *
     * @return array
     */
    protected function buildJsonCache(string $language, string $application): array
    {
        // init var
        $json = [];
        foreach (Model::TYPES as $type) {
            // loop locale
            foreach ($this->locale as $i => $item) {
                // types match
                if ($item['type'] == $type) {
                    if ($application == 'Backend') {
                        $json[$type][$item['module']][$item['name']] = $item['value'];
                    } else {
                        $json[$type][$item['name']] = $item['value'];
                    }
                }
            }
        }

        $this->addSpoonLocale($json, $language);

        return $json;
    }

    /**
     * Adds months and days from spoonLocale to the json
     *
     * @param array  $json
     * @param string $language
     */
    protected function addSpoonLocale(array &$json, string $language): void
    {
        // get months
        $monthsLong = \SpoonLocale::getMonths($language, false);
        $monthsShort = \SpoonLocale::getMonths($language, true);

        // get days
        $daysLong = \SpoonLocale::getWeekDays($language, false, 'sunday');
        $daysShort = \SpoonLocale::getWeekDays($language, true, 'sunday');

        // build labels
        foreach ($monthsLong as $key => $value) {
            $json['loc']['MonthLong' . \SpoonFilter::ucfirst($key)] = $value;
        }
        foreach ($monthsShort as $key => $value) {
            $json['loc']['MonthShort' . \SpoonFilter::ucfirst($key)] = $value;
        }
        foreach ($daysLong as $key => $value) {
            $json['loc']['DayLong' . \SpoonFilter::ucfirst($key)] = $value;
        }
        foreach ($daysShort as $key => $value) {
            $json['loc']['DayShort' . \SpoonFilter::ucfirst($key)] = $value;
        }
    }

    /**
     * dumps the locale in cache as a json object
     *
     * @param string $language
     * @param string $application
     */
    protected function dumpJsonCache(string $language, string $application): void
    {
        $filesystem = new Filesystem();
        $filesystem->dumpFile(
            constant(mb_strtoupper($application) . '_CACHE_PATH') . '/Locale/' . $language . '.json',
            json_encode($this->buildJsonCache($language, $application))
        );
    }
}
