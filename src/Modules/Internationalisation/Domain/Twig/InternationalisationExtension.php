<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Twig;

use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class InternationalisationExtension extends AbstractExtension
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'tolabel',
                fn (string $string): string => TranslationKey::label(Container::camelize($string))->trans(
                    $this->translator
                )
            ),
        ];
    }
}
