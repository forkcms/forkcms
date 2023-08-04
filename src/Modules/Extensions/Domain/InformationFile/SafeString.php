<?php

namespace ForkCMS\Modules\Extensions\Domain\InformationFile;

use SimpleXMLElement;
use Stringable;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

final class SafeString implements Stringable
{
    private function __construct(public readonly string $string)
    {
    }

    public static function fromXML(SimpleXMLElement $XMLElement): self
    {
        $sanitizer = new HtmlSanitizer((new HtmlSanitizerConfig()));

        return new self($sanitizer->sanitize(nl2br(trim($XMLElement))));
    }

    public function __toString(): string
    {
        return $this->string;
    }
}
