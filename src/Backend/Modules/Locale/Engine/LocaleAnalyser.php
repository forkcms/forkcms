<?php

namespace Backend\Modules\Locale\Engine;

use Symfony\Component\Finder\Finder;

final class LocaleAnalyser
{
    /** @var string */
    private $application;

    /** @var string */
    private $modulesPath;

    /** @var array */
    private $installedModules;

    public function __construct(string $application, string $modulesPath, array $installedModules)
    {
        $this->application = $application;
        $this->modulesPath = $modulesPath;
        $this->installedModules = $installedModules;
    }

    public function findMissingLocale(string $language): array
    {
        $moduleFiles = $this->findModuleFiles();

        // @TODO
        return [];
    }

    /**
     * Get a string between two key strings
     *
     * @param string $start front key string
     * @param string $end back key string
     * @param string $str the string that needs to be checked
     *
     * @return mixed return string or false
     */
    private function getInBetweenStrings(string $start, string $end, string $str)
    {
        $matches = [];
        preg_match_all("@$start([a-zA-Z0-9_]*)$end@", $str, $matches);

        return isset($matches[1]) ? current($matches[1]) : '';
    }

    private function findModuleFiles(): array
    {
        $moduleFiles = [];
        $finder = new Finder();
        $finder
            ->name('*.php')
            ->name('*.html.twig')
            ->name('*.js');

        foreach ($finder->files()->in($this->modulesPath)->getIterator() as $file) {
            $module = $this->getInBetweenStrings('Modules/', '/', $file->getPath());
            if (!in_array($module, $this->installedModules, true)) {
                continue;
            }
            $filename = $file->getFilename();
            $moduleFiles[$module][$filename] = $file;
        }

        return $moduleFiles;
    }
}
