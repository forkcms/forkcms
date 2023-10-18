<?php

namespace ForkCMS\Modules\Blog\Domain\ModuleSettings;

use ForkCMS\Core\Domain\Form\FieldsetType;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Extensions\Domain\Module\Command\ChangeModuleSettings;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModuleSettingsType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $settings = $builder->getData();
        /** @var User $user */
        $user = $this->security->getUser();
        $workingLocale = $this->translator->getLocale();

        $builder
            ->add(
                'pagination',
                FieldsetType::class,
                [
                    'label' => 'lbl.Pagination',
                    'fields' => static function (FormBuilderInterface $builder) use ($settings): void {
                        $builder
                            ->add(
                                'overview_num_items',
                                ChoiceType::class,
                                [
                                    'label' => 'lbl.ItemsPerPage',
                                    'required' => false,
                                    'choices' => array_combine(range(1, 30), range(1, 30)),
                                    'placeholder' => false,
                                    'data' => $settings->__get('overview_num_items') ?? 20,
                                ]
                            )
                            ->add(
                                'recent_articles_full_num_items',
                                ChoiceType::class,
                                [
                                    'label' => 'msg.NumItemsInRecentArticlesFull',
                                    'required' => false,
                                    'choices' => array_combine(range(1, 10), range(1, 10)),
                                    'placeholder' => false,
                                    'data' => $settings->__get('recent_articles_full_num_items') ?? 5,
                                ]
                            )
                            ->add(
                                'recent_articles_list_num_items',
                                ChoiceType::class,
                                [
                                    'label' => 'msg.NumItemsInRecentArticlesList',
                                    'required' => false,
                                    'choices' => array_combine(range(1, 10), range(1, 10)),
                                    'placeholder' => false,
                                    'data' => $settings->__get('recent_articles_list_num_items') ?? 5,
                                ]
                            )
                        ;
                    },
                ]
            )
            ->add(
                'comments',
                FieldsetType::class,
                [
                    'label' => 'lbl.Comments',
                    'fields' => static function (FormBuilderInterface $builder): void {
                        $builder
                            ->add(
                                'allow_comments',
                                CheckboxType::class,
                                [
                                    'required' => false,
                                    'label' => 'lbl.AllowComments',
                                ]
                            )
                            ->add(
                                'moderation',
                                CheckboxType::class,
                                [
                                    'required' => false,
                                    'label' => 'lbl.EnableModeration',
                                ]
                            )
                        ;
                    }
                ]
            )
        ;

        if ($user->isSuperAdmin()) {
            $builder->add(
                'image',
                FieldsetType::class,
                [
                    'label' => 'lbl.Image',
                    'fields' => static function (FormBuilderInterface $builder): void {
                        $builder->add(
                            'show_image_form',
                            CheckboxType::class,
                            [
                                'label' => 'msg.ShowImageForm',
                                'required' => false,
                            ]
                        );
                    }
                ]
            );
        }

        $builder
            ->add(
                'notifications',
                FieldsetType::class,
                [
                    'label' => 'lbl.Notifications',
                    'fields' => static function (FormBuilderInterface $builder): void {
                        $builder
                            ->add(
                                'notify_by_email_on_new_comment_to_moderate',
                                CheckboxType::class,
                                [
                                    'required' => false,
                                    'label' => 'msg.NotifyByEmailOnNewCommentToModerate',
                                ]
                            )
                            ->add(
                                'notify_by_email_on_new_comment',
                                CheckboxType::class,
                                [
                                    'required' => false,
                                    'label' => 'msg.NotifyByEmailOnNewComment',
                                ]
                            )
                        ;
                    }
                ]
            )
            ->add(
                'rssFeed',
                FieldsetType::class,
                [
                    'label' => 'lbl.RSSFeed',
                    'fields' => static function (FormBuilderInterface $builder) use ($settings, $workingLocale): void {
                        $builder
                            ->add(
                                'rss_title_' . $workingLocale,
                                TextType::class,
                                [
                                    'label' => 'lbl.Title',
                                    'required' => true,
                                    'data' => $settings->__get('rss_title_' . $workingLocale) ?? 'RSS',
                                    'help' => 'msg.HelpRSSTitle',
                                ]
                            )
                            ->add(
                                'rss_description_' . $workingLocale,
                                TextareaType::class,
                                [
                                    'label' => 'lbl.Description',
                                    'required' => false,
                                    'data' => $settings->__get('rss_description_' . $workingLocale) ?? '',
                                    'help' => 'msg.HelpRSSDescription',
                                    'attr' => [
                                        'rows' => 5,
                                    ]
                                ]
                            )
                            ->add(
                                'rss_meta_' . $workingLocale,
                                CheckboxType::class,
                                [
                                    'label' => 'lbl.Meta',
                                    'help' => 'msg.HelpMeta',
                                    'required' => false,
                                    'data' => $settings->__get('rss_meta_' . $workingLocale) ?? true,
                                ]
                            )
                        ;
                    }
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('data_class', ChangeModuleSettings::class);
    }
}
