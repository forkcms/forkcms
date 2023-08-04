<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Core\Engine\Model;
use Backend\Modules\Profiles\Domain\Group\Group;
use Common\Form\SwitchType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageDataAuthenticationType extends AbstractType implements PageDataTypeInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!Model::isModuleInstalled('Profiles')) {
            return;
        }

        $builder->add(
            'required',
            SwitchType::class,
            [
                'label' => 'msg.AuthRequired',
                'required' => false,
                'attr' => [
                    'data-bs-toggle' => 'collapse',
                    'data-bs-target' => '[data-role=authentication-options]',
                ],
            ]
        );
        $builder->add(
            'removeFromSearchIndex',
            SwitchType::class,
            [
                'label' => 'msg.RemoveFromSearchIndex',
                'required' => false,
            ]
        );
        $builder->add(
            'groups',
            EntityType::class,
            [
                'required' => false,
                'label' => 'lbl.Groups',
                'class' => Group::class,
                'multiple' => true,
                'expanded' => true,
                'choice_value' => static function ($id): ?int {
                    if ($id instanceof Group) {
                        return $id->getId();
                    }

                    return $id;
                },
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'inherit_data' => true,
                'label' => false,
            ]
        );
    }

    public static function transform(?array $persistedData, array $transformedData): array
    {
        if ($persistedData === null || !Model::isModuleInstalled('Profiles')) {
            $transformedData['required'] = false;
            $transformedData['remove_from_search_index'] = false;
            $transformedData['groups'] = [];

            return $transformedData;
        }

        $transformedData['required'] = $persistedData['auth_required'] ?? false;
        $transformedData['removeFromSearchIndex'] = $persistedData['remove_from_search_index'] ?? false;
        $transformedData['groups'] = $persistedData['auth_groups'] ?? [];

        return $transformedData;
    }

    public static function reverseTransform(array $submittedData, array $transformedData): array
    {
        if (!Model::isModuleInstalled('Profiles')) {
            return $transformedData;
        }

        $transformedData['auth_required'] = $submittedData['required'];

        if (!$submittedData['required']) {
            $transformedData['remove_from_search_index'] = false;

            return $transformedData;
        }

        $transformedData['remove_from_search_index'] = $submittedData['removeFromSearchIndex'];
        $transformedData['auth_groups'] = array_map(
            static function (Group $group): int {
                return $group->getId();
            },
            $submittedData['groups']
        );

        return $transformedData;
    }
}
