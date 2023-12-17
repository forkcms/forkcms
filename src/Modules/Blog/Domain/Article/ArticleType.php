<?php

namespace ForkCMS\Modules\Blog\Domain\Article;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use ForkCMS\Core\Domain\Form\EditorType;
use ForkCMS\Core\Domain\Form\TabsType;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Blog\Domain\Article\Command\ArticleDataTransferObject;
use ForkCMS\Modules\Blog\Domain\Category\Category;
use ForkCMS\Modules\Blog\Frontend\Actions\Detail;
use ForkCMS\Modules\Frontend\Domain\Meta\MetaType;
use ForkCMS\Modules\Pages\Domain\Page\PageRouter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly PageRouter $pageRouter
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ArticleDataTransferObject $data */
        $data = $builder->getData();

        /** @var User $user */
        $user = $this->security->getUser();
        if ($data->createdBy === null) {
            $data->createdBy = $user;
        }
        $data->updatedBy = $user;

        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'lbl.Title',
                ]
            )
        ;
        $builder->add(
            'tabs',
            TabsType::class,
            [
                'tabs' => [
                    'lbl.Content' => static function (FormBuilderInterface $builder): void {
                        // added through the pre-set data event
                    },
                    'lbl.Comments' => static function (FormBuilderInterface $builder): void {
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
            function (FormEvent $event) use ($data): void {
                $tabs = $event->getForm()->get('tabs');

                $tabs->get(md5('lbl.Content'))
                    ->add(
                        'introduction',
                        EditorType::class,
                        [
                            'label' => 'lbl.Summary',
                        ]
                    )
                    ->add(
                        'text',
                        EditorType::class,
                        [
                            'label' => 'lbl.MainContent',
                        ]
                    )
                    ->add(
                        'status',
                        ChoiceType::class,
                        [
                            'label' => 'lbl.Status',
                            'expanded' => true,
                            'choices' => [
                                'lbl.Draft' => Status::DRAFT,
                                'lbl.Active' => Status::ACTIVE,
                            ]
                        ]
                    )
                    ->add(
                        'hidden',
                        ChoiceType::class,
                        [
                            'label' => 'lbl.Visibility',
                            'expanded' => true,
                            'choices' => [
                                'lbl.Hidden' => true,
                                'lbl.Published' => false,
                            ],
                        ]
                    )
                    ->add(
                        'publishOn',
                        DateTimeType::class,
                        [
                            'label' => 'lbl.PublishOn',
                            'date_widget' => 'single_text',
                            'time_widget' => 'single_text'
                        ]
                    )
                    ->add(
                        'category',
                        EntityType::class,
                        [
                            'label' => 'lbl.Category',
                            'class' => Category::class,
                            'choice_label' => 'title',
                            'placeholder' => 'lbl.ChooseACategory',
                            'query_builder' => function (EntityRepository $er) use ($data): QueryBuilder {
                                return $er->createQueryBuilder('c')
                                    ->andWhere('c.locale = :locale')
                                    ->orderBy('c.title', 'ASC')
                                    ->setParameter('locale', $data->locale);
                            },
                        ]
                    )
                    ->add(
                        'updatedBy',
                        EntityType::class,
                        [
                            'label' => 'lbl.Author',
                            'class' => User::class,
                            'choice_label' => 'displayname',
                        ]
                    )
                ;

                $tabs->get(md5('lbl.Comments'))
                    ->add(
                        'allowComments',
                        CheckboxType::class,
                        [
                            'required' => false,
                            'label' => 'lbl.AllowComments'
                        ]
                    )
                ;

                $tabs->get(md5('lbl.SEO'))->add('meta', MetaType::class, [
                    'disable_slug_overwrite' => false,
                    'base_field_name' => 'title',
                    'base_url' => $this->pageRouter->getRouteForBlock(
                        Detail::getModuleBlock(),
                        $data->locale
                    ),
                    'generate_slug_callback_class' => ArticleRepository::class,
                    'generate_slug_callback_method' => 'generateSlug',
                    'generate_slug_callback_parameters' => [
                        $data->locale,
                        $data->hasEntity() ? $data->getEntity()->getRevisionId() : null,
                    ],
                ]);
            }
        );

        // TODO image
        // status
        // publish on ??
        //
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('data_class', ArticleDataTransferObject::class);
    }
}
