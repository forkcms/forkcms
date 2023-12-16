<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock;

use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Core\Domain\Form\DataGridType;
use ForkCMS\Core\Domain\Form\EditorType;
use ForkCMS\Core\Domain\Form\SwitchType;
use ForkCMS\Core\Domain\Form\TabsType;
use ForkCMS\Core\Domain\Form\TitleType;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Twig\ForkTemplateLoader;
use Pageon\DoctrineDataGridBundle\DataGrid\DataGrid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBlockType extends AbstractType
{
    public function __construct(private readonly ForkTemplateLoader $forkTemplateLoader)
    {
    }

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

        $templates = $this->forkTemplateLoader->getPossibleTemplates(
            ModuleName::fromFQCN(self::class),
            Application::FRONTEND,
            'Widgets'
        );

        $builder
            ->add('title', TitleType::class)
            ->add(
                'text',
                EditorType::class,
                ['required' => true, 'label' => 'lbl.Content']
            );
        if (count($templates) > 1) {
            $builder->add('template', ChoiceType::class, [
                'required' => true,
                'label' => 'lbl.Template',
                'choices' => $templates,
                'choice_translation_domain' => false,
                'preferred_choices' => [ContentBlock::DEFAULT_TEMPLATE],
            ]);
        }
        $builder->add('isVisible', SwitchType::class, $isVisibleOptions);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', ContentBlockDataTransferObject::class);
        $resolver->setDefault('revisions_data_grid', null);
        $resolver->setAllowedTypes('revisions_data_grid', [DataGrid::class, 'null']);
    }
}
