<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Exporter;

use DOMDocument;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;

final class XmlExporter implements ExporterInterface
{
    /** @param iterable<Translation> $translations */
    public function exportTranslations(iterable $translations): string
    {
        $xml = new DOMDocument('1.0', 'utf-8');

        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;

        $root = $xml->createElement('translations');
        $xml->appendChild($root);

        $currentApplication = null;
        $currentModule = null;
        $currentTranslationKey = null;
        $applicationElement = null;
        $moduleElement = null;
        $translationItemElement = null;

        /** @var Translation $translation */
        foreach ($translations as $translation) {
            if ($currentApplication !== $translation->getDomain()->getApplication()) {
                $currentApplication = $translation->getDomain()->getApplication();
                $applicationElement = $xml->createElement($currentApplication->value);
                $root->appendChild($applicationElement);
                $translationItemElement = null;
            }
            if ($currentModule?->getName() !== $translation->getDomain()->getModuleName()?->getName()) {
                $currentModule = $translation->getDomain()->getModuleName();
                if ($currentModule !== null) {
                    $moduleElement = $xml->createElement($currentModule->getName());
                    $applicationElement->appendChild($moduleElement);
                } else {
                    $moduleElement = null;
                }
                $translationItemElement = null;
            }

            if ($translationItemElement === null || !$translation->getKey()->equals($currentTranslationKey)) {
                $translationItemElement = $xml->createElement('item');
                if ($moduleElement !== null) {
                    $moduleElement->appendChild($translationItemElement);
                } else {
                    $applicationElement->appendChild($translationItemElement);
                }
                $translationItemElement->setAttribute('type', $translation->getKey()->getType()->value);
                $translationItemElement->setAttribute('name', $translation->getKey()->getName());

                $currentTranslationKey = $translation->getKey();
            }

            $translationElement = $xml->createElement('translation');
            $translationElement->setAttribute('locale', $translation->getLocale()->value);
            if ($translation->getSource() !== null) {
                $translationElement->setAttribute('source', $translation->getSource());
            }
            $translationElement->nodeValue = sprintf('<![CDATA[%1$s]]>', $translation->getValue());
            $translationItemElement->appendChild($translationElement);
        }

        return $xml->saveXML();
    }

    public static function forExtension(): string
    {
        return 'xml';
    }
}
