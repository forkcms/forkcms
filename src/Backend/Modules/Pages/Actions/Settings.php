<?php

namespace Backend\Modules\Pages\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupRepository;
use Backend\Modules\Pages\Domain\Page\Form\SettingsType;
use Backend\Modules\Pages\Domain\Page\SettingsDataTransferObject;
use Common\BlockEditor\Twig\ParseBlocksExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Twig\Environment;

/**
 * This is the settings-action, it will display a settingsForm to set general pages settings
 */
class Settings extends BackendBaseActionEdit
{
    /** @var Form */
    private $settingsForm;

    public function execute(): void
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        $this->settingsForm = $this->createForm(
            SettingsType::class,
            new SettingsDataTransferObject(
                $this->get('fork.settings'),
                $this->get('media_library.repository.group')
            )
        );

        $this->template->assign('form', $this->settingsForm->createView());
    }

    private function validateForm(): void
    {
        $this->settingsForm->handleRequest($this->getRequest());

        // settingsForm is submitted
        if ($this->settingsForm->isSubmitted() && $this->settingsForm->isValid()) {
            /** @var SettingsDataTransferObject $data */
            $data = $this->settingsForm->getData();

            // set our settings
            $this->get('fork.settings')->set(
                $this->getModule(),
                'meta_navigation',
                $data->metaNavigation
            );

            $this->get('fork.settings')->set(
                $this->getModule(),
                'offline_title_' . Language::getWorkingLanguage(),
                $data->offlineTitle
            );

            $this->get('fork.settings')->set(
                $this->getModule(),
                'offline_text_' . Language::getWorkingLanguage(),
                $data->offlineText
            );

            if ($data->offlineImage instanceof MediaGroup) {
                $this->get('media_library.repository.group')->add($data->offlineImage);
                $this->get('doctrine.orm.entity_manager')->flush();
                $this->get('fork.settings')->set(
                    $this->getModule(),
                    'offline_image_' . Language::getWorkingLanguage(),
                    $data->offlineImage->getId()
                );
            }

            $this->renderOfflinePage();

            // redirect to the settings page
            $this->redirect(BackendModel::createUrlForAction('Settings') . '&report=saved');
        }
    }

    private function renderOfflinePage(): void
    {
        /** @var Environment $twig */
        $twig = $this->get('twig');

        /** @var ParseBlocksExtension $extension */
        $extension = $twig->getExtension(ParseBlocksExtension::class);

        $html = $twig->render(
            BACKEND_MODULES_PATH . '/' . $this->getModule() .'/Layout/Templates/Offline.html.twig',
            [
                'title' => $this->get('fork.settings')->get(
                    $this->getModule(),
                    'offline_title_' . Language::getWorkingLanguage()
                ),
                'text' => $extension->parseBlocks(
                    $this->get('fork.settings')->get(
                        $this->getModule(),
                        'offline_text_' . Language::getWorkingLanguage()
                    )
                )
                 ,
                'image' => $this->get('media_library.repository.group')->findOneById(
                    $this->get('fork.settings')->get(
                        $this->getModule(),
                        'offline_image_' . Language::getWorkingLanguage()
                    )
                )
            ]
        );

        $offlinePage = fopen(OFFLINE_PATH . '/offline_' . Language::getWorkingLanguage() . '.html', 'w');
        fwrite($offlinePage, $html);
        fclose($offlinePage);
    }
}
