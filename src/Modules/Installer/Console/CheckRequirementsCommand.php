<?php

namespace ForkCMS\Modules\Installer\Console;

use ForkCMS\Modules\Installer\Domain\Requirement\Requirement;
use ForkCMS\Modules\Installer\Domain\Requirement\RequirementCategory;
use ForkCMS\Modules\Installer\Domain\Requirement\RequirementsChecker;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** This command will run the requirements checks of fork.*/
final class CheckRequirementsCommand extends Command
{
    public const RETURN_SERVER_DOES_NOT_MEET_REQUIREMENTS = 2;
    public const RETURN_SERVER_MEETS_REQUIREMENTS = 0;
    public const RETURN_SERVER_MEETS_REQUIREMENTS_BUT_HAS_WARNINGS = 1;

    private SymfonyStyle $formatter;

    public function __construct(private readonly RequirementsChecker $requirementsChecker)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('forkcms:installer:check-requirements')
            ->setDescription('Command to check if the server meets the install requirements');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->formatter = new SymfonyStyle($input, $output);

        return $this->serverMeetsRequirements();
    }

    private function serverMeetsRequirements(): int
    {
        if ($this->requirementsChecker->passes()) {
            $this->formatter->success('This server meets the Fork CMS requirements');

            return self::RETURN_SERVER_MEETS_REQUIREMENTS;
        }

        if ($this->requirementsChecker->hasWarnings() && !$this->requirementsChecker->hasErrors()) {
            $this->formatter->success('This server meets the Fork CMS requirements');
            $this->formatter->note('There are some warnings, see below for more info');

            $this->showWarnings();

            return self::RETURN_SERVER_MEETS_REQUIREMENTS_BUT_HAS_WARNINGS;
        }

        if ($this->requirementsChecker->hasErrors()) {
            $this->formatter->error('This server does not meet the Fork CMS requirements');

            $this->showWarnings();
            $this->showErrors();

            return self::RETURN_SERVER_DOES_NOT_MEET_REQUIREMENTS;
        }

        throw new RuntimeException('Invalid requirement state');
    }

    private function showWarnings(): void
    {
        $this->formatter->section('Warnings');
        array_map(
            function (RequirementCategory $requirementCategory) {
                if (!$requirementCategory->hasWarnings()) {
                    return;
                }

                $this->formatter->title($requirementCategory->name);

                array_map(
                    function (Requirement $requirement) {
                        $this->formatter->warning($requirement->name);
                        $this->formatter->block($this->formatRequirementMessageForCLI($requirement->message));
                    },
                    $requirementCategory->getWarnings()
                );
            },
            $this->requirementsChecker->getRequirementCategories()
        );
    }

    private function showErrors(): void
    {
        $this->formatter->section('Errors');
        array_map(
            function (RequirementCategory $requirementCategory) {
                if (!$requirementCategory->hasErrors()) {
                    return;
                }

                $this->formatter->title($requirementCategory->name);

                array_map(
                    function (Requirement $requirement) {
                        $this->formatter->error($requirement->name);
                        $this->formatter->block($this->formatRequirementMessageForCLI($requirement->message));
                    },
                    $requirementCategory->getErrors()
                );
            },
            $this->requirementsChecker->getRequirementCategories()
        );
    }

    private function formatRequirementMessageForCLI(string $message): string
    {
        $message = str_replace(['                 ', "\n"], ['', "\n\n"], $message);

        // format urls as "text (url)" or just url if the text and url are the same
        return preg_replace_callback(
            '|<a[\s\S]*?href="(.*?)"[\s\S]*?>(.*?)</a>|',
            static function ($matches) {
                if ($matches[1] !== $matches[2]) {
                    return $matches[2] . ' (' . $matches[1] . ')';
                }

                return $matches[1];
            },
            $message
        );
    }
}
