<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation\Event;

use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use Symfony\Contracts\EventDispatcher\Event;

final class TranslationCreatedEvent extends Event
{
    /** @var Translation[] */
    public readonly array $translations;

    public function __construct(Translation ...$translations)
    {
        $this->translations = $translations;
    }
}
