<?php

namespace ForkCMS\Modules\Backend\Domain\UserGroup;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserGroupDataGridChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(
            [
                'class' => UserGroup::class,
                'multiple' => true,
                'expanded' => true,
                'label' => false,
                'choice_label' => static fn (UserGroup $userGroup): string => $userGroup->getName(),
            ]
        );
    }

    public function getParent(): string
    {
        return EntityType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'user_group_data_grid_choice';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $possibleGroups = $options['choice_loader']->loadChoiceList()->getChoices();

        $view->vars['possibleGroups'] = array_combine(
            array_map(static fn (UserGroup $userGroup): int => $userGroup->getId(), $possibleGroups),
            $possibleGroups
        );
    }
}
