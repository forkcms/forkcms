<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock;

use ForkCMS\Core\Domain\Form\EditorType;
use ForkCMS\Core\Domain\Form\TitleType;
use ForkCMS\Modules\Backend\Domain\User\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentBlockType extends AbstractType
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ContentBlockDataTransferObject $data */
        $data = $builder->getData();

        /** @var User $user */
        $user = $this->security->getUser();
        if ($data->createdBy === null) {
            $data->createdBy = $user;
        }
        $data->updatedBy = $user;

        $builder->add(
            'title',
            TitleType::class
        )->add(
            'text',
            EditorType::class,
            [
                'required' => true,
                'label' => 'lbl.Content',
            ]
        );

        $isVisibleOptions = [
            'label' => 'lbl.VisibleOnSite',
            'required' => false,
        ];

        if (!array_key_exists('data', $options)) {
            $isVisibleOptions['attr']['checked'] = 'checked';
        }

        $builder->add(
            'isVisible',
            CheckboxType::class,
            $isVisibleOptions
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', ContentBlockDataTransferObject::class);
    }
}
