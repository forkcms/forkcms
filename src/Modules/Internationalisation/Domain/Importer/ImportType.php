<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Importer;

use ForkCMS\Core\Domain\Form\SwitchType;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

final class ImportType extends AbstractType
{
    public function __construct(private readonly Importer $importer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'file',
            FileType::class,
            [
                'label' => 'lbl.File',
                'attr' => [
                    'accept' => '.' . implode(',.', $this->importer->getAvailableExtensions()),
                ],
                'constraints' => [
                    new File(),
                ]
            ]
        )->add(
            'overwrite',
            SwitchType::class,
            [
                'required' => false,
                'label' => TranslationKey::label('Overwrite'),
            ]
        );
    }
}
