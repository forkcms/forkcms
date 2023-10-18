<?php

namespace ForkCMS\Modules\Blog\Domain\ModuleSettings;

use ForkCMS\Core\Domain\Form\FieldsetType;
use ForkCMS\Modules\Extensions\Domain\Module\Command\ChangeModuleSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModuleSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $settings = $builder->getData();

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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('data_class', ChangeModuleSettings::class);
    }
}
