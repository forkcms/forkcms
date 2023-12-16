<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock;

use ForkCMS\Core\Domain\Form\DataGridType;
use ForkCMS\Core\Domain\Form\EditorType;
use ForkCMS\Core\Domain\Form\SwitchType;
use ForkCMS\Core\Domain\Form\TabsType;
use ForkCMS\Core\Domain\Form\TitleType;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Pageon\DoctrineDataGridBundle\DataGrid\DataGrid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['revisions_data_grid'] === null) {
            $this->buildContentForm($builder, $options);

            return;
        }

        $builder->add('contentBlock', TabsType::class, [
            'tabs' => [
                'lbl.Content' => function (FormBuilderInterface $builder) use ($options): void {
                    $this->buildContentForm($builder, $options);
                },
                'lbl.Revisions' => function (FormBuilderInterface $builder) use ($options): void {
                    $builder->add(
                        'revisions',
                        DataGridType::class,
                        [
                            'mapped' => false,
                            'data_grid' => $options['revisions_data_grid'],
                        ]
                    );
                },
            ],
        ]);
    }

    /** @param array<string,mixed> $options */
    private function buildContentForm(FormBuilderInterface $builder, array $options): void
    {
        $isVisibleOptions = [
            'label' => 'lbl.VisibleOnSite',
            'required' => false,
        ];

        if (!array_key_exists('data', $options)) {
            $isVisibleOptions['attr']['checked'] = 'checked';
        }

        $builder
            ->add('title', TitleType::class)
            ->add(
                'text',
                EditorType::class,
                ['required' => true, 'label' => 'lbl.Content']
            )
            ->add('isVisible', SwitchType::class, $isVisibleOptions);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', ContentBlockDataTransferObject::class);
        $resolver->setDefault('revisions_data_grid', null);
        $resolver->setAllowedTypes('revisions_data_grid', [DataGrid::class, 'null']);
    }
}
