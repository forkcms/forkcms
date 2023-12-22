<?php

namespace ForkCMS\Modules\Pages\Domain\Revision\Event;

use ForkCMS\Modules\Frontend\Domain\Block\Event\BeforeDeleteBlockEvent;
use ForkCMS\Modules\Frontend\Domain\Block\Event\IsBlockInUseEvent;
use ForkCMS\Modules\Pages\Domain\Revision\RevisionRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(
    event: IsBlockInUseEvent::class,
    method: 'isBlockInUse'
)]
#[AsEventListener(
    event: BeforeDeleteBlockEvent::class,
    method: 'onBlockDelete'
)]
final readonly class FrontendBlockEventListener
{
    public function __construct(private RevisionRepository $revisionRepository)
    {
    }

    public function isBlockInUse(IsBlockInUseEvent $findBlockUsagesEvent): void
    {
        if (count($this->revisionRepository->findRevisionsForFrontendBlock($findBlockUsagesEvent->block)) > 0) {
            $findBlockUsagesEvent->registerUsage();
        }
    }

    public function onBlockDelete(BeforeDeleteBlockEvent $beforeDeleteBlockEvent): void
    {
        $this->revisionRepository->deleteFrontendBlockFromRevisions($beforeDeleteBlockEvent->block);
    }
}
