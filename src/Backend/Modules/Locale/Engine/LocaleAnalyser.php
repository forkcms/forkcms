<?php

namespace Backend\Modules\Locale\Engine;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
        $locale = $this->findLocaleInFiles($moduleFiles);

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

    private function getDefaultModule(string $currentModuleName): string
    {
        return $this->application === 'Backend' ? $currentModuleName : 'Core';
    }

    private function getLocaleArrayFromRegexMatches(string $defaultModuleName, array $matches): array
    {
        // remove the full match
        $matchesCount = count(array_shift($matches));

        [$types, $labels, $modules] = $matches;

        $locale = [];

        for ($index = 0; $index < $matchesCount; ++$index) {
            $module = empty($modules[$index]) ? $defaultModuleName : $modules[$index];
            $locale[$types[$index]][$module][$labels[$index]] = $labels[$index];
        }

        return $locale;
    }

    private function findLocaleInJsFile(string $moduleName, string $filename, string $fileContent): array
    {
        $locale = [];
        $patterns = [
            '"\.locale\.(act|err|lbl|msg)\(\'(\w+?)\'(?:, ?\'(\w+?)\')?\)"',
            '"\.locale\.get\(\'(\w+?)\', ?\'(\w+?)\'(?:, ?\'(\w+?)\')?\)"',
        ];
        foreach ($patterns as $pattern) {
            $matches = [];
            preg_match_all($pattern, $fileContent, $matches);
            $locale[] = $this->getLocaleArrayFromRegexMatches($this->getDefaultModule($moduleName), $matches);
        }

        if (count($locale) === 0) {
            return [];
        }

        return [
            'file' => $filename,
            'locale' => array_merge_recursive(...$locale),
        ];
    }

    private function findLocaleInFile(string $moduleName, string $filename, SplFileInfo $file): array
    {
        switch ($file->getExtension()) {
            case 'js':
                return $this->findLocaleInJsFile($moduleName, $filename, $file->getContents());
            default:
                return [];
        }
    }

    private function findLocaleInModuleFiles(string $moduleName, array $module): array
    {
        $locale = [];
        foreach ($module as $filename => $file) {
            $fileLocale = $this->findLocaleInFile($moduleName, $filename, $file);


            if (!empty($fileLocale)) {
                $locale[$filename] = $fileLocale;
            }
        }

        return $locale;
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

    private function findLocaleInFiles(array $moduleFiles): array
    {
        $locale = [];

        foreach ($moduleFiles as $moduleName => $module) {
            $locale[$moduleName] = $this->findLocaleInModuleFiles($moduleName, $module);
        }

        return $locale;
    }
}
