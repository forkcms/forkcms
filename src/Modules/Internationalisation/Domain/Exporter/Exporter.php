<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Exporter;

use Assert\Assertion;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class Exporter
{
    /** @param ServiceLocator<ExporterInterface> $exporters */
    public function __construct(private readonly ServiceLocator $exporters)
    {
    }

    /** @param iterable<Translation> $translations */
    public function export(iterable $translations, string $extension): string
    {
        /** @var ExporterInterface $exporter */
        $exporter = $this->exporters->get($extension);
        Assertion::implementsInterface($exporter, ExporterInterface::class);

        return $exporter->exportTranslations($translations);
    }

    /** @return string[] */
    public function getAvailableExtensions(): array
    {
        return array_keys($this->exporters->getProvidedServices());
    }
}
