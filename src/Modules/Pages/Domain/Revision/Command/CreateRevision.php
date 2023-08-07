<?php

namespace ForkCMS\Modules\Pages\Domain\Revision\Command;

use DateTimeImmutable;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;
use ForkCMS\Modules\Pages\Domain\Revision\RevisionDataTransferObject;

final class CreateRevision extends RevisionDataTransferObject
{
    private function __construct(
        ?Revision $revisionEntity = null,
        public readonly bool $clearCache = true
    ) {
        parent::__construct($revisionEntity);
    }

    public static function new(
        Page $page,
        Locale $locale,
        ThemeTemplate $themeTemplate,
        bool $clearCache = true
    ): self {
        $revision = new self(null, $clearCache);
        $revision->page = $page;
        $revision->locale = $locale;
        $revision->themeTemplate = $themeTemplate;
        $revision->settings['hidden'] = false;
        $revision->settings['publishOn'] = new DateTimeImmutable();
        $revision->settings['allowMove'] = !$page->isForbiddenToMove();
        $revision->settings['allowChildren'] = !$page->isForbiddenToHaveChildren();
        $revision->settings['allowEdit'] = true;
        $revision->settings['allowDelete'] = !$page->isForbiddenToDelete();

        return $revision;
    }

    public static function fromRevision(Revision $revision, bool $clearCache = true): self
    {
        return new self($revision, $clearCache);
    }

    public function setEntity(Revision $revision): void
    {
        $this->revisionEntity = $revision;
    }
}
