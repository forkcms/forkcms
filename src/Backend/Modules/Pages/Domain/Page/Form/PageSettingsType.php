<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Modules\Pages\Domain\Page\PageDataTransferObject;
use Common\Form\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'hidden',
            ChoiceType::class,
            [
                'choices' => [
                    'lbl.Hidden' => true,
                    'lbl.Published' => false,
                ],
                'label' => false,
                'label_attr' => [
                    'class' => 'custom-control-label radio-custom',
                ],
                'expanded' => true,
            ]
        );
        $builder->add(
            'publishOn',
            DateTimeType::class,
            [
                'label' => 'lbl.PublishOn',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
            ]
        );
        $builder->add(
            'publishUntil',
            DateTimeType::class,
            [
                'label' => 'lbl.PublishTill',
                'required' => false,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
            ]
        );

        if (!BackendAuthentication::getUser()->isGod()) {
            return;
        }

        $builder->add(
            'allowMove',
            SwitchType::class,
            [
                'label' => 'msg.Allow_move',
                'attr' => [
                    'data-role' => 'allow-move-toggle',
                ],
                'required' => false,
            ]
        );
        $builder->add(
            'allowChildren',
            SwitchType::class,
            [
                'label' => 'msg.Allow_children',
                'attr' => [
                    'data-role' => 'allow-children-toggle',
                ],
                'required' => false,
            ]
        );
        $builder->add(
            'allowEdit',
            SwitchType::class,
            [
                'label' => 'msg.Allow_edit',
                'attr' => [
                    'data-role' => 'allow-edit-toggle',
                ],
                'required' => false,
            ]
        );
        $builder->add(
            'allowDelete',
            SwitchType::class,
            [
                'label' => 'msg.Allow_delete',
                'attr' => [
                    'data-role' => 'allow-delete-toggle',
                ],
                'required' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => PageDataTransferObject::class,
                'inherit_data' => true,
            ]
        );
    }
}
