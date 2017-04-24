<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MediaItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $label = 'lbl.Title';

        if ($builder->getData()->getMediaItemEntity()->getType()->isMovie()) {
            $label = 'lbl.MediaMovieTitle';
            $this->addField($builder, 'url', 'lbl.MediaMovieId');
        }

        $this->addField($builder, 'title', $label);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param $name
     * @param $label
     */
    private function addField(FormBuilderInterface $builder, $name, $label)
    {
        $builder
            ->add(
                $name,
                TextType::class,
                [
                    'label' => $label,
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'media_item';
    }
}
