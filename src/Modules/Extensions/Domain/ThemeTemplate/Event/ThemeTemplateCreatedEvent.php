<?php

namespace ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Event;

use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Command\CreateThemeTemplate;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use Symfony\Contracts\EventDispatcher\Event;

final class ThemeTemplateCreatedEvent extends Event
{
    public function __construct(public readonly ThemeTemplate $themeTemplate)
    {
    }

    public static function fromCreateCommand(CreateThemeTemplate $createThemeTemplate): self
    {
        return new self($createThemeTemplate->getEntity());
    }
}
