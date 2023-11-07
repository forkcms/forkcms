<?php

namespace ForkCMS\Modules\Pages\Domain\Revision\Form;

use ForkCMS\Core\Domain\Form\TabsType;
use ForkCMS\Core\Domain\Form\TitleType;
use ForkCMS\Modules\Frontend\Domain\Meta\MetaType;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\Page\PageRouter;
use ForkCMS\Modules\Pages\Domain\Revision\Command\CreateRevision;
use ForkCMS\Modules\Pages\Domain\Revision\RevisionDataTransferObject;
use ForkCMS\Modules\Pages\Domain\Revision\RevisionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RevisionType extends AbstractType
{
    public function __construct(private readonly PageRouter $pageRouter)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TitleType::class);
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
                    'lbl.Settings' => static function (FormBuilderInterface $builder): void {
                        // added through the pre-set data event
                    }
                ],
                'left_tabs_count' => 1,
            ]
        );
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void {
                $revisionDataTransferObject = $event->getData();
                $tabs = $event->getForm()->get('tabs');
                $tabs->get(md5('lbl.Content'))->add(
                    'content',
                    RevisionContentType::class,
                    [
                        'selectedTemplate' => $revisionDataTransferObject->themeTemplate,
                        'load_default_blocks' => !$revisionDataTransferObject->page->hasId(),
                    ]
                );
                $tabs->get(md5('lbl.Settings'))->add(
                    'settings',
                    RevisionSettingsType::class,
                    [
                        'disable_allow_move' => $revisionDataTransferObject->page->isForbiddenToMove(),
                        'disable_allow_delete' => $revisionDataTransferObject->page->isForbiddenToDelete(),
                        'disable_allow_children' => $revisionDataTransferObject->page->isForbiddenToHaveChildren(),
                    ]
                );

                $entity = $revisionDataTransferObject->hasEntity();
                $tabs->get(md5('lbl.SEO'))->add('meta', MetaType::class, [
                    'disable_slug_overwrite' => $revisionDataTransferObject->page->isHome(),
                    'base_field_name' => 'title',
                    'base_url' => $this->pageRouter->getRouteForPageId(
                        $revisionDataTransferObject->parentPage !== null
                            ? $revisionDataTransferObject->parentPage->getId()
                            : Page::PAGE_ID_HOME
                    ),
                    'generate_slug_callback_class' => RevisionRepository::class,
                    'generate_slug_callback_method' => 'generateSlug',
                    'generate_slug_callback_parameters' => [
                        $revisionDataTransferObject->locale,
                        $entity ? $entity->getId() : null,
                    ],
                ]);
                if ($revisionDataTransferObject->hasEntity()) {
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
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => RevisionDataTransferObject::class,
            ]
        );
    }
}
