<?php

namespace ForkCMS\Modules\Internationalisation\Backend\Ajax;

use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Modules\Backend\Domain\AjaxAction\AbstractAjaxActionController;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Command\ChangeTranslation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Command\CreateTranslation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationDomain;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationRepository;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Edit a translation over ajax
 */
final class TranslationEdit extends AbstractAjaxActionController
{
    public function __construct(
        private readonly TranslationRepository $translationRepository,
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    protected function execute(Request $request): void
    {
        $moduleName = ModuleName::fromString($request->query->get('moduleName'));
        $fields = [
            'domain' => new TranslationDomain(
                Application::from($request->query->get('application')),
                $moduleName === ModuleName::core() ? null : $moduleName
            ),
            'key' => TranslationKey::forType(
                Type::from($request->query->get('type')),
                $request->query->get('name')
            ),
            'locale' => Locale::from($request->query->get('locale')),
        ];

        $existingTranslations = $this->translationRepository->uniqueDataTransferObjectMethod($fields);

        if (count($existingTranslations) > 0) {
            $this->updateTranslation($existingTranslations[0], $request->request->get('content'));

            return;
        }

        $createTranslation = new CreateTranslation();
        $createTranslation->key = $fields['key'];
        $createTranslation->value = $request->request->get('content');
        $createTranslation->locale = Locale::from($request->query->get('locale'));
        $createTranslation->domain = $fields['domain'];
        $this->commandBus->dispatch($createTranslation);
    }

    private function updateTranslation(Translation $translation, string $content): void
    {
        $editTranslation = new ChangeTranslation($translation);
        $editTranslation->value = $content;
        $this->commandBus->dispatch($editTranslation);
    }
}
