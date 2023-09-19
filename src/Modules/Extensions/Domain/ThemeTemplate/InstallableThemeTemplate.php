<?php

namespace ForkCMS\Modules\Extensions\Domain\ThemeTemplate;

use ForkCMS\Modules\Extensions\Domain\InformationFile\SafeString;
use SimpleXMLElement;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;

final class InstallableThemeTemplate extends ThemeTemplateDataTransferObject
{
    /* @phpstan-ignore-next-line */
    public readonly bool $isDefault;

    public static function fromXML(SimpleXMLElement|bool|null $template): ?self
    {
        if (!$template instanceof SimpleXMLElement) {
            return null;
        }

        $themeTemplate = new self();
        $themeTemplate->name = SafeString::fromXML($template->attributes()->name);
        $themeTemplate->path = str_replace(
            ThemeTemplate::PATH_DIRECTORY,
            '',
            SafeString::fromXML($template->attributes()->path)
        );
        $themeTemplate->active = true;
        $layout = str_replace(' ', '', trim(SafeString::fromXML($template->layout)));
        $themeTemplate->settings->set('layout', $layout);
        $positions = [];
        $serialiser = new Serializer([], [new XmlEncoder()]);
        foreach ($template->positions as $xmlPositions) {
            foreach ($xmlPositions->position as $xmlPosition) {
                $blocks = [];
                foreach ($xmlPosition->block as $xmlBlock) {
                    $blocks[] = $serialiser->decode($xmlBlock->saveXML(), 'xml');
                }
                $positions[] = [
                    'name' => SafeString::fromXML($xmlPosition->attributes()->name),
                    'blocks' => $blocks,
                ];
            }
        }
        if (count($positions) === 0) {
            $positions = array_map(
                static fn (string $position): array => ['name' => $position],
                ThemeTemplateDataTransferObject::getPositionsFromFormat($layout)
            );
        }
        $themeTemplate->settings->set('positions', $positions);
        /* @phpstan-ignore-next-line */
        $themeTemplate->isDefault = $template->attributes()?->default?->__toString() === 'true';

        return $themeTemplate;
    }
}
