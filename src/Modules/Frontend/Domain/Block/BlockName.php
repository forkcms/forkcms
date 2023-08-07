<?php

namespace ForkCMS\Modules\Frontend\Domain\Block;

use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

interface BlockName
{
    public function getType(): Type;

    public function getName(): string;

    public static function fromString(string $name): static;

    public function __toString(): string;

    public function asLabel(): TranslationKey;
}
