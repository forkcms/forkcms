<?php

namespace ForkCMS\Modules\Internationalisation\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Internationalisation\Domain\Importer\Importer;
use ForkCMS\Modules\Internationalisation\Domain\Importer\ImportType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TranslationImport extends AbstractFormActionController
{
    public function __construct(ActionServices $actionServices, private readonly Importer $importer)
    {
        parent::__construct($actionServices);
    }

    protected function getFormResponse(Request $request): ?Response
    {
        return $this->handleForm(
            $request,
            ImportType::class,
            validCallback: function (FormInterface $form): ?Response {
                $this->importer->import(
                    $form->getData()['file'],
                    $form->getData()['overwrite']
                );
                $this->assign(
                    'importResults',
                    $this->importer->import(
                        $form->getData()['file']->getRealPath(),
                        $form->getData()['overwrite']
                    )
                );


                return null;
            },
        );
    }
}
