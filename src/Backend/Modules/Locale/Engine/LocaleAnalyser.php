<?php

namespace Backend\Modules\Locale\Engine;

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
        // @TODO
        return [];
    }
}
