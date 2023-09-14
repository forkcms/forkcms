<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Importer;

use Assert\Assertion;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleRepository;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleRepository;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Event\TranslationChangedEvent;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Event\TranslationCreatedEvent;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationRepository;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Importer
{
    /** @param ServiceLocator<ImporterInterface> $importers */
    public function __construct(
        private readonly ServiceLocator $importers,
        private readonly string $cacheDir,
        private readonly TranslationRepository $translationRepository,
        private readonly InstalledLocaleRepository $installedLocaleRepository,
        private readonly ModuleRepository $moduleRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function import(
        string|UploadedFile|File $translationFile,
        bool $overwriteConflicts = false,
        ?Locale $specificLocale = null
    ): ImportResult {
        if (is_string($translationFile)) {
            $translationFile = new File($translationFile);
        }
        $importResult = new ImportResult();

        /** @var ImporterInterface $importer */
        $importer = $this->importers->get($translationFile->guessExtension());
        Assertion::implementsInterface($importer, ImporterInterface::class);

        $translations = $importer->getTranslations($translationFile);
        $locales = $this->installedLocaleRepository->findAllIndexed();
        $modules = $this->moduleRepository->findAllIndexed();
        $fallbackLocale = Locale::fallback()->value;
        $existingTranslations = [];
        $newTranslations = [];
        foreach ($translations as $translation) {
            $application = $translation->getDomain()->getApplication();
            $moduleName = $translation->getDomain()->getModuleName();
            $locale = $translation->getLocale()->value;
            if (
                ($moduleName instanceof ModuleName && !array_key_exists($moduleName->getName(), $modules))
                || ($specificLocale !== null && $specificLocale !== $translation->getLocale())
                || (
                    $locale !== $fallbackLocale
                    && (
                        !array_key_exists($locale, $locales)
                        || ($application === Application::FRONTEND && !$locales[$locale]->isEnabledForWebsite())
                        || ($application === Application::BACKEND && !$locales[$locale]->isEnabledForUser())
                    )
                )
            ) {
                $importResult->addSkipped();
                continue;
            }

            $existingTranslation = $existingTranslations[$translation->getId()]
                ?? $newTranslations[$translation->getId()]
                ?? $this->translationRepository->find($translation->getId());

            if ($existingTranslation !== null) {
                if ($overwriteConflicts) {
                    $existingTranslation->change($translation->getValue());
                    $existingTranslations[$translation->getId()] = $existingTranslation;
                    $importResult->addUpdated();

                    continue;
                }

                $importResult->addFailed($translation);
                continue;
            }

            $newTranslations[$translation->getId()] = $translation;
            $importResult->addImported();
        }

        $this->translationRepository->save(...$existingTranslations, ...$newTranslations);
        $this->eventDispatcher->dispatch(new TranslationChangedEvent(...$existingTranslations));
        $this->eventDispatcher->dispatch(new TranslationCreatedEvent(...$newTranslations));
        $filesystem = new Filesystem();
        $translationsDirectory = $this->cacheDir . '/translations';
        if ($filesystem->exists($translationsDirectory)) {
            $filesystem->remove($translationsDirectory);
        }

        if ($this->translator instanceof Translator) {
            // resets the translator cache
            $this->translator->setFallbackLocales($this->translator->getFallbackLocales());
        }

        return $importResult;
    }

    /** @return string[] */
    public function getAvailableExtensions(): array
    {
        return array_keys($this->importers->getProvidedServices());
    }
}
