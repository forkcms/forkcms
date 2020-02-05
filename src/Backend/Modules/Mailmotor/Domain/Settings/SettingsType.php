<?php

namespace Backend\Modules\Mailmotor\Domain\Settings;

use Backend\Modules\Mailmotor\Domain\Settings\Command\SaveSettings;
use Common\Language;
use MailMotor\Bundle\MailMotorBundle\Manager\SubscriberGatewayManager;
use SpoonFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class SettingsType extends AbstractType
{
    /**
     * @var SubscriberGatewayManager
     */
    protected $subscriberGatewayManager;

    public function __construct(SubscriberGatewayManager $subscriberGatewayManager)
    {
        $this->subscriberGatewayManager = $subscriberGatewayManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mailEngines = $this->getPossibleMailEngines();

        // if we have multiple templates, add a dropdown to select them
        if (count($mailEngines) > 0) {
            $builder->add(
                'mailEngine',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => 'lbl.MailEngine',
                    'choices' => $mailEngines,
                    'choice_translation_domain' => false,
                ]
            );
        }

        $builder->add(
            'apiKey',
            TextType::class,
            [
                'required' => true,
                'label' => 'lbl.ApiKey',
            ]
        )->add(
            'listId',
            TextType::class,
            [
                'required' => true,
                'label' => 'lbl.Default',
            ]
        )->add(
            'languageListIds',
            CollectionType::class,
            [
                'entry_type' => TextType::class,
                'allow_add' => false,
                'allow_delete' => false,
                'required' => false,
                'label' => 'lbl.ListId',
            ]
        );

        $doubleOptIn = [
            'label' => 'msg.DoubleOptIn',
            'required' => false,
        ];
        $overwriteInterests = [
            'label' => 'msg.OverwriteInterests',
            'required' => false,
        ];

        $builder->add(
            'doubleOptIn',
            CheckboxType::class,
            $doubleOptIn
        );

        $builder->add(
            'overwriteInterests',
            CheckboxType::class,
            $overwriteInterests
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaveSettings::class,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                if ($data->mailEngine === 'not_implemented') {
                    return ['Default'];
                }

                return ['Default', 'mail_engine_selected'];
            },
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'settings';
    }

    private function getPossibleMailEngines(): array
    {
        $ddmValuesForMailEngines = [];
        $mailEnginesWithSubscriberGateway = $this->subscriberGatewayManager->getAll();

        foreach ($mailEnginesWithSubscriberGateway as $key => $mailEngine) {
            $label = SpoonFilter::ucfirst(($key === 'not_implemented') ? Language::lbl('None') : $key);

            $ddmValuesForMailEngines[$label] = $key;
        }

        return $ddmValuesForMailEngines;
    }
}
