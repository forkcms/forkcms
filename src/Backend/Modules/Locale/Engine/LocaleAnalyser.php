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

    /** @var string */
    private $siteModulespath;

    /** @var array */
    private $installedModules;

    public function __construct(string $application, string $modulesPath, array $installedModules)
    {
        $this->application = $application;
        $this->modulesPath = $modulesPath;
        $this->installedModules = $installedModules;
        $this->siteModulespath = substr($modulesPath, strpos($modulesPath, 'src'));
    }

    public function findMissingLocale(string $language): array
    {
        $this->missingLocale = [];
        $moduleFiles = $this->findModuleFiles();
        $existingLocale = $this->getExistingLocaleForLanguage($language);

        $locale = $this->findLocaleInFiles($moduleFiles);

        $nonExisting = [];
        foreach ($locale as $moduleName => $module) {
            foreach ($module as $filename => $file) {
                $file['locale'] = $this->localeArrayDiff($file['locale'], $existingLocale, $moduleName);

                foreach ($file['locale'] as $type => $modules) {
                    foreach ($modules as $key => $translations) {
                        foreach ($translations as $translationName) {
                            $nonExistingKey = $this->application . $translationName . $type . $key;

                            if (array_key_exists($nonExistingKey, $nonExisting)) {
                                $usedInFiles = unserialize($nonExisting[$nonExistingKey]['used_in']);
                                $usedInFiles[] = $file['file'];
                                $nonExisting[$nonExistingKey]['used_in'] = serialize($usedInFiles);

                                continue;
                            }

                            $nonExisting[$nonExistingKey] = [
                                'language' => $language,
                                'application' => $this->application,
                                'module' => $key,
                                'type' => $type,
                                'name' => $translationName,
                                'used_in' => serialize([$file['file']]),
                            ];
                        }
                    }
                }
            }
        }

        ksort($nonExisting);

        return $nonExisting;
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

    private function getPatternsForExtension(string $extension): array
    {
        switch ($extension) {
            case 'js':
                return [
                    '"\.locale\.(act|err|lbl|msg)\([\'\"](\w+?)[\'\"](?:, ?[\'\"](\w+?)[\'\"])?\)"',
                    '"\.locale\.get\([\'\"](\w+?)[\'\"], ?[\'\"](\w+?)[\'\"](?:, ?[\'\"](\w+?)[\'\"])?\)"',
                ];

                break;
            case 'php':
                $argumentsPattern = '\([\'\"](\w+?)[\'\"](?:, ?[\'\"](\w+?)[\'\"]|\))';

                return [
                    '"(?:Language|FL|BL)::(get(?:Label|Error|Message|Action)|act|err|lbl|msg)' . $argumentsPattern . '"'
                ];
            case 'twig':
                return [
                    '"[\'\"](act|err|lbl|msg)\.(\w+?)[\'\"]\|trans()"'
                ];
            default:
                return [];
        }
    }

    private function findLocaleInFile(string $moduleName, string $filename, SplFileInfo $file): array
    {
        $locale = [];
        $fileContent = $file->getContents();
        $patterns = $this->getPatternsForExtension($file->getExtension());

        foreach ($patterns as $pattern) {
            $matches = [];
            preg_match_all($pattern, $fileContent, $matches);
            $localeMatches = $this->getLocaleArrayFromRegexMatches($this->getDefaultModule($moduleName), $matches);
            if (!empty($localeMatches)) {
                $locale[] = $localeMatches;
            }
        }

        if (empty($locale)) {
            return [];
        }

        return [
            'file' => $this->siteModulespath . '/' . $file->getRelativePath() . '/' . $filename,
            'locale' => array_merge_recursive(...$locale),
        ];
    }

    private function getLocaleArrayFromRegexMatches(string $defaultModuleName, array $matches): array
    {
        // remove the full match
        $matchesCount = count(array_shift($matches));

        [$types, $labels, $modules] = $matches;

        $locale = [];

        for ($index = 0; $index < $matchesCount; ++$index) {
            $module = empty($modules[$index]) ? $defaultModuleName : $modules[$index];
            $locale[$this->cleanUpLocaleType($types[$index])][$module][$labels[$index]] = $labels[$index];
        }

        return $locale;
    }

    private function cleanUpLocaleType(string $type): string
    {
        return str_replace(['getMessage', 'getLabel', 'getError', 'getAction'], ['msg','lbl', 'err', 'act'], $type);
    }

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

    private function getExistingLocaleForLanguage(string $language): array
    {
        return array_map(
            function (array $localeForType) {
                $localeSortedByModule = [];
                foreach ($localeForType as $locale) {
                    $localeSortedByModule[$locale['module']][$locale['name']] = $locale['name'];
                }

                return $localeSortedByModule;
            },
            Model::getTranslations(
                $this->application,
                '',
                ['lbl', 'act', 'err', 'msg'],
                [$language],
                '',
                ''
            )
        );
    }

    private function localeArrayDiff(array $fileLocale, array $existingLocale, string $currentModule): array
    {
        $diff = [];
        foreach ($fileLocale as $type => $modules) {
            $typeDiff = $this->recursiveArrayDiff($modules, $existingLocale[$type] ?? []);

            if (array_key_exists($currentModule, $typeDiff)) {
                $typeDiff[$currentModule] = $this->recursiveArrayDiff(
                    $typeDiff[$currentModule],
                    $existingLocale[$type]['Core'] ?? []
                );
            }

            $diff[$type] = $typeDiff;
        }

        return $diff;
    }

    private function recursiveArrayDiff(array $array1, array $array2): array
    {
        $outputDiff = [];

        foreach ($array1 as $key => $value) {
            if (!array_key_exists($key, $array2) || !is_array($value)) {
                if (!in_array($value, $array2, true)) {
                    $outputDiff[$key] = $value;
                }

                continue;
            }

            if (!is_array($value)) {
                continue;
            }

            $recursiveDiff = $this->recursiveArrayDiff($value, $array2[$key]);

            if (count($recursiveDiff)) {
                $outputDiff[$key] = $recursiveDiff;
            }
        }

        return $outputDiff;
    }
}
