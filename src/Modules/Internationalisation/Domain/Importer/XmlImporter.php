<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Importer;

use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationDomain;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Type;
use Generator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

final class XmlImporter implements ImporterInterface
{
    public function __construct(private string $rootDir)
    {
    }

    public function getTranslations(File $translationFile): Generator
    {
        $source = str_replace(realpath($this->rootDir), '', $translationFile->getRealPath());
        $xmlDecoder = new XmlEncoder();
        $xmlData = $xmlDecoder->decode($translationFile->getContent(), 'xml');

        foreach ($xmlData as $application => $modules) {
            $application = Application::from(strtolower($application));
            if (array_key_exists('item', $modules)) {
                yield from $this->makeTranslations(
                    $modules['item'],
                    TranslationDomain::fromDomain($application->value),
                    $translationFile instanceof UploadedFile ? null : $source
                );
                unset($modules['item']);
            }

            foreach ($modules as $module => $translationItems) {
                if ($module === 'item') {
                    continue;
                }

                $domain = new TranslationDomain($application, ModuleName::fromString($module));
                yield from $this->makeTranslations(
                    $translationItems['item'],
                    $domain,
                    $translationFile instanceof UploadedFile ? null : $source
                );
            }
        }
    }

    public static function forExtension(): string
    {
        return 'xml';
    }

    /**
     * @param array<string, mixed> $translationItems
     *
     * @return Generator<Translation>
     */
    public function makeTranslations(array $translationItems, TranslationDomain $domain, ?string $source): Generator
    {
        if (array_key_exists('@type', $translationItems)) {
            $translationItems = [$translationItems];
        }

        foreach ($translationItems as $translationItem) {
            $key = TranslationKey::forType(Type::from($translationItem['@type']), $translationItem['@name']);

            foreach ($translationItem['translation'] as $translation) {
                yield new Translation($domain, $key, Locale::from($translation['@locale']), $translation['#'], $source);
            }
        }
    }
}
