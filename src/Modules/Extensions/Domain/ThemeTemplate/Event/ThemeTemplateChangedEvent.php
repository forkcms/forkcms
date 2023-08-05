<?php

namespace ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Event;

use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Command\ChangeThemeTemplate;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use Symfony\Contracts\EventDispatcher\Event;

final class ThemeTemplateChangedEvent extends Event
{
    public function __construct(public readonly ThemeTemplate $themeTemplate, public readonly bool $overwrite)
    {
    }

    public static function fromChangeCommand(ChangeThemeTemplate $changeThemeTemplate): self
    {
        return new self($changeThemeTemplate->getEntity(), $changeThemeTemplate->overwrite);
    }
}
