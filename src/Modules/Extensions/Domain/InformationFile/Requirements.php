<?php

namespace ForkCMS\Modules\Extensions\Domain\InformationFile;

use Composer\Semver\Comparator;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use SimpleXMLElement;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

final class Requirements implements \JsonSerializable
{
    public function __construct(public readonly string $minimumVersion, public readonly string $maximumVersion)
    {
    }

    public static function fromXML(SimpleXMLElement $requirements, Messages $messages): self
    {
        $sanitiser = new HtmlSanitizer((new HtmlSanitizerConfig()));
        $minimumVersion = $sanitiser->sanitize($requirements->minimum_version ?? '');
        if ($minimumVersion !== '' && Comparator::lessThan($_ENV['FORK_VERSION'], $minimumVersion)) {
            $messages->addMessage(
                TranslationKey::error('InformationVersionTooLow')->withParameters(
                    ['%minimumVersion%' => $minimumVersion, '%currentVersion%' => $_ENV['FORK_VERSION']]
                )
            );
        }
        $maximumVersion = $sanitiser->sanitize($requirements->maximum_version ?? '');
        if ($maximumVersion !== '' && Comparator::greaterThanOrEqualTo($_ENV['FORK_VERSION'], $maximumVersion)) {
            $messages->addMessage(
                TranslationKey::error('InformationVersionTooHigh')->withParameters(
                    ['%maximumVersion%' => $maximumVersion, '%currentVersion%' => $_ENV['FORK_VERSION']]
                )
            );
        }

        return new self($minimumVersion, $maximumVersion);
    }

    /** @return array<string, string> */
    public function jsonSerialize(): array
    {
        return [
            'minimum_version' => $this->minimumVersion,
            'maximum_version' => $this->maximumVersion,
        ];
    }
}
