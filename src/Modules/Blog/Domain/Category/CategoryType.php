<?php

namespace ForkCMS\Modules\Blog\Domain\Category;

use ForkCMS\Core\Domain\Form\TabsType;
use ForkCMS\Modules\Blog\Domain\Category\Command\CategoryDataTransferObject;
use ForkCMS\Modules\Frontend\Domain\Meta\MetaType;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\Revision\Form\RevisionContentType;
use ForkCMS\Modules\Pages\Domain\Revision\Form\RevisionSettingsType;
use ForkCMS\Modules\Pages\Domain\Revision\RevisionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', HiddenType::class);
        $builder->add(
            'tabs',
            TabsType::class,
            [
                'tabs' => [
                    'lbl.Content' => static function (FormBuilderInterface $builder): void {
                        // added through the pre-set data event
                    },
                    'lbl.SEO' => static function (FormBuilderInterface $builder): void {
                        // added through the pre-set data event
                    },
                ],
                'left_tabs_count' => 1,
            ]
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void {
                $categoryData = $event->getData();
                $tabs = $event->getForm()->get('tabs');
                $tabs->get(md5('lbl.Content'))->add(
                    'title',
                    TextType::class,
                    [
                        'label' => 'lbl.Title',
                    ]
                );
                $tabs->get(md5('lbl.SEO'))->add('meta', MetaType::class, [
                    'disable_slug_overwrite' => false,
                    'base_field_name' => 'title',
                    'base_url' => '',   // TODO: get route for category
                    /*'base_url' => $this->pageRouter->getRouteForPageId(
                        $revisionDataTransferObject->parentPage !== null
                            ? $revisionDataTransferObject->parentPage->getId()
                            : Page::PAGE_ID_HOME
                    ),*/
                    'generate_slug_callback_class' => CategoryRepository::class,
                    'generate_slug_callback_method' => 'generateSlug',
                    'generate_slug_callback_parameters' => [
                    ],
                ]);
                /*if ($revisionDataTransferObject->hasEntity()) {
                    $event->getForm()->add(
                        'saveAsDraft',
                        SubmitType::class,
                        [
                            'label' => 'lbl.SaveDraft',
                            'attr' => [
                                'class' => 'btn-default',
                            ],
                        ]
                    );
                }*/
            }
        );
        /*
        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'lbl.Title',
            ]
        )
        ->add(
            'meta',
            MetaType::class,
            [
                'label' => 'lbl.Meta',
            ]
        );
        */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('data_class', CategoryDataTransferObject::class);
    }
}
