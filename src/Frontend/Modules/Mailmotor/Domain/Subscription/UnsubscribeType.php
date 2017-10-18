<?php

namespace Frontend\Modules\Mailmotor\Domain\Subscription;

use Frontend\Modules\Mailmotor\Domain\Subscription\Command\Unsubscription;
use Common\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnsubscribeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'email',
            EmailType::class,
            [
                'required' => true,
                'label' => 'lbl.Email',
                'attr' => [
                    'placeholder' => \SpoonFilter::ucfirst(Language::lbl('YourEmail')),
                ],
            ]
        )->add(
            'unsubscribe',
            SubmitType::class,
            [
                'label' => \SpoonFilter::ucfirst(Language::lbl('Unsubscribe')),
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Unsubscription::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'unsubscribe';
    }
}
