<?php

namespace App\Component\Header;

use App\Service\Module\ModuleSettings;
use Frontend\Core\Engine\Theme;

final class Facebook
{
    /** @var ModuleSettings */
    private $moduleSettings;

    public function __construct(ModuleSettings $moduleSettings)
    {
        $this->moduleSettings = $moduleSettings;
    }

    public function addOpenGraphMeta(Header $header): void
    {
        $parseFacebook = false;
        $facebookAdminIds = $this->moduleSettings->get('Core', 'facebook_admin_ids', null);
        $facebookAppId = $this->moduleSettings->get('Core', 'facebook_app_id', null);

        // check if facebook admins are set
        if ($facebookAdminIds !== null) {
            $header->addMetaData(
                [
                    'property' => 'fb:admins',
                    'content' => $facebookAdminIds,
                ],
                true,
                ['property']
            );
            $parseFacebook = true;
        }

        // check if no facebook admin is set but an app is configured we use the application as an admin
        if ($facebookAdminIds === '' && $facebookAppId !== null) {
            $header->addMetaData(
                [
                    'property' => 'fb:app_id',
                    'content' => $facebookAppId,
                ],
                true,
                ['property']
            );
            $parseFacebook = true;
        }

        if (!$parseFacebook) {
            return;
        }

        $header->addOpenGraphData('locale', $this->getLocale());
        $header->addOpenGraphImage('/src/Frontend/Themes/' . Theme::getTheme() . '/facebook.png');
        $header->addOpenGraphImage('/facebook.png');
    }

    private function getLocale(): string
    {
        $specialCases = [
            'en' => 'en_US',
            'zh' => 'zh_CN',
            'cs' => 'cs_CZ',
            'el' => 'el_GR',
            'ja' => 'ja_JP',
            'sv' => 'sv_SE',
            'uk' => 'uk_UA',
        ];

        if (array_key_exists(LANGUAGE, $specialCases)) {
            return str_replace(array_keys($specialCases), $specialCases, LANGUAGE);
        }

        return mb_strtolower(LANGUAGE) . '_' . mb_strtoupper(LANGUAGE);
    }
}
