<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Form\EventListener\AddMetaSubscriber;
use Backend\Form\Type\TagsType;
use Backend\Modules\Pages\Domain\Page\PageDataTransferObject;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use Common\Form\TitleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TitleType::class);
        $builder->addEventSubscriber(
            new AddMetaSubscriber(
                'Pages', // Virtual to make sure the correct url is used
                'Page', // Virtual to make sure the correct url is used
                PageRepository::class,
                'getUrl',
                [
                    'getData.getLocale',
                    'getData.getId',
                    'getData.getParentId',
                    'getData.isAction',
                ]
            )
        );
        $builder->add('navigation', PageNavigationType::class);
        $builder->add('data', PageDataType::class);
        $builder->add('settings', PageSettingsType::class);
        $builder->add('tags', TagsType::class, ['label' => 'msg.AddTagsHere', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => PageDataTransferObject::class,
            ]
        );
    }
}
