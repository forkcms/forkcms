<?php

namespace Backend\Modules\Pages\Domain\Page;

use Backend\Core\Language\Locale;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupRepository;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type as MediaGroupType;
use Common\ModulesSettings;

class SettingsDataTransferObject
{
    const MODULE = 'Pages';

    public $metaNavigation;

    public $offlineTitle;

    public $offlineText;

    /** @var MediaGroup */
    public $offlineImage;

    public function __construct(ModulesSettings $settings, MediaGroupRepository $mediaGroupRepository)
    {
        $this->metaNavigation = $settings->get(
            $this::MODULE,
            'meta_navigation'
        );

        $this->offlineTitle = $settings->get(
            $this::MODULE,
            'offline_title_' . Locale::workingLocale()
        );

        $this->offlineText = $settings->get(
            $this::MODULE,
            'offline_text_' . Locale::workingLocale()
        );

        $mediaId = $settings->get(
            $this::MODULE,
            'offline_image_' . Locale::workingLocale()
        );

        $this->offlineImage = MediaGroup::create(MediaGroupType::image());
        if ($mediaId !== null) {
            try {
                $this->offlineImage = $mediaGroupRepository->findOneById($mediaId);
            } catch (\Exception $e) {

            }

        }
    }
}
