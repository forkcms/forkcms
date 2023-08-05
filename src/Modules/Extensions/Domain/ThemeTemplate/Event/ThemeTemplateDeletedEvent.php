<?php

namespace ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Event;

use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Command\DeleteThemeTemplate;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use Symfony\Contracts\EventDispatcher\Event;

final class ThemeTemplateDeletedEvent extends Event
{
    public function __construct(public readonly ThemeTemplate $themeTemplate)
    {
    }

    public static function fromDeleteCommand(DeleteThemeTemplate $deleteThemeTemplate): self
    {
        return new self($deleteThemeTemplate->getEntity());
    }
}
