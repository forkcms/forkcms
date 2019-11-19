<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtra;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use Backend\Modules\Pages\Domain\Page\Page;
use Common\Locale;
use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyModuleContentToOtherLocaleHandlerInterface;
use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyModuleContentToOtherLocaleInterface;
use RuntimeException;

final class CopyContentBlocksToOtherLocaleHandler implements CopyModuleContentToOtherLocaleHandlerInterface
{
    /** @var ContentBlockRepository */
    private $contentBlockRepository;

    /** @var ModuleExtraRepository */
    private $moduleExtraRepository;

    public function __construct(
        ContentBlockRepository $contentBlockRepository,
        ModuleExtraRepository $moduleExtraRepository
    ) {
        $this->contentBlockRepository = $contentBlockRepository;
        $this->moduleExtraRepository = $moduleExtraRepository;
    }

    public function handle(CopyModuleContentToOtherLocaleInterface $command): void
    {
        if (!$command instanceof CopyContentBlocksToOtherLocale) {
            throw new RuntimeException('The class should be ' . CopyContentBlocksToOtherLocale::class);
        }

        $contentBlocksToCopy = $this->getContentBlocksToCopy($command->getFromLocale(), $command->getPageToCopy());
        $id = $this->contentBlockRepository->getNextIdForLanguage($command->getToLocale());

        /** @var ContentBlock $contentBlock */
        foreach ($contentBlocksToCopy as $contentBlock) {
            $moduleExtra = $this->moduleExtraRepository->find($contentBlock->getExtraId());

            if (!$moduleExtra instanceof ModuleExtra) {
                continue;
            }

            $originalContentBlockData = $moduleExtra->getData();

            [$isNewModuleExtra, $newModuleExtraId] = $this->getExistingModuleExtraIdOrCreate(
                $command->getToLocale()->getLocale(),
                $originalContentBlockData
            );

            $command->setModuleExtraId($contentBlock->getExtraId(), $newModuleExtraId);

            if (!$isNewModuleExtra) {
                continue;
            }

            $dataTransferObject = $contentBlock->getDataTransferObject();

            // Overwrite some variables
            $dataTransferObject->forOtherLocale(
                $id++,
                $command->getModuleExtraId($contentBlock->getExtraId()),
                $command->getToLocale()
            );

            $this->contentBlockRepository->add(ContentBlock::fromDataTransferObject($dataTransferObject));

            // Save the copied id so that we don't copy the module extra again
            $originalContentBlockData['copies'][$command->getToLocale()->getLocale()] = $newModuleExtraId;
            $moduleExtra->setData('copies', $originalContentBlockData['copies']);

            $this->moduleExtraRepository->save($moduleExtra);
        }
    }

    /**
     * @return ContentBlock[]
     */
    private function getContentBlocksToCopy(Locale $locale, ?Page $filterByPage): array
    {
        if (!$filterByPage instanceof Page) {
            return $this->contentBlockRepository->findAllActiveForLocale($locale);
        }

        $contentBlocks = [];

        $moduleExtras = $this->moduleExtraRepository->findModuleExtra(
            'ContentBlocks',
            'Detail',
            ModuleExtraType::widget()
        );

        foreach ($moduleExtras as $moduleExtra) {
            if (!array_key_exists('id', $moduleExtra->getData())) {
                continue;
            }

            $contentBlockId = $moduleExtra->getData()['id'];

            $contentBlock = $this->contentBlockRepository->find($contentBlockId);

            if (!$contentBlock instanceof ContentBlock) {
                continue;
            }

            $contentBlocks[] = $contentBlock;
        }

        return $contentBlocks;
    }

    private function getNewExtraId(): int
    {
        return Model::insertExtra(
            ModuleExtraType::widget(),
            'ContentBlocks',
            'Detail'
        );
    }

    /**
     * @param string $locale
     * @param mixed $originalContentBlockData
     *
     * @return array With two elements. First: is it a new ID? Second: The ID.
     */
    private function getExistingModuleExtraIdOrCreate(string $locale, $originalContentBlockData): array
    {
        $copies = $originalContentBlockData['copies'] ?? [];

        if (array_key_exists($locale, $copies)) {
            return [false, $copies[$locale]];
        }

        return [true, $this->getNewExtraId()];
    }
}
