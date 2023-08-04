<?php

namespace ForkCMS\Modules\Extensions\Domain\Theme\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Theme\Event\ThemeInstalledEvent;
use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use ForkCMS\Modules\Extensions\Domain\Theme\ThemeRepository;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Event\ThemeTemplateCreatedEvent;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\InstallableThemeTemplate;
use ForkCMS\Modules\Frontend\Domain\Action\ActionName;
use ForkCMS\Modules\Frontend\Domain\Block\BlockRepository;
use ForkCMS\Modules\Frontend\Domain\Block\ModuleBlock;
use ForkCMS\Modules\Frontend\Domain\Block\Type;
use ForkCMS\Modules\Frontend\Domain\Widget\WidgetName;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class InstallThemeHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ThemeRepository $themeRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly BlockRepository $blockRepository
    ) {
    }

    public function __invoke(InstallTheme $installTheme): void
    {
        foreach ($installTheme->theme->templates as $template) {
            if (!$template instanceof InstallableThemeTemplate) {
                continue;
            }
            $template->setPositions(
                array_map(function (array $position): array {
                    if (!array_key_exists('blocks', $position)) {
                        return $position;
                    }

                    $blocks = [];
                    foreach ($position['blocks'] as $block) {
                        $blockName = match (Type::from($block['@type'])) {
                            Type::ACTION => ActionName::fromString($block['@name']),
                            Type::WIDGET => WidgetName::fromString($block['@name']),
                        };
                        $blocks[] = $this->blockRepository->findUnique(
                            new ModuleBlock(ModuleName::fromString($block['@module']), $blockName),
                            new SettingsBag(
                                array_filter(
                                    $block,
                                    static fn ($key) => !str_starts_with($key, '@'),
                                    ARRAY_FILTER_USE_KEY
                                )
                            )
                        )?->getId();
                    }
                    $position['blocks'] = array_filter($blocks, is_int(...));

                    return $position;
                }, $template->getPositions())
            );
        }
        $theme = Theme::fromDataTransferObject($installTheme->theme);
        $installTheme->theme->setTheme($theme);
        $this->themeRepository->save($theme);

        $this->eventDispatcher->dispatch(new ThemeInstalledEvent($theme));
        foreach ($theme->getTemplates() as $themeTemplate) {
            $this->eventDispatcher->dispatch(new ThemeTemplateCreatedEvent($themeTemplate));
        }
    }
}
