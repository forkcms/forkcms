<?php

namespace Frontend\Modules\MailMotor\Form;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\ModulesSettings;
use Frontend\Modules\MailMotor\Command\Subscription;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;
use MailMotor\Bundle\MailMotorBundle\Helper\Subscriber;
use Common\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscribeType extends AbstractType
{
    /**
     * @var array
     */
    protected $interests;

    /**
     * @var ModulesSettings
     */
    protected $modulesSettings;

    /**
     * @var Subscriber
     */
    protected $subscriber;

    /**
     * SubscribeType constructor.
     * @param Subscriber $subscriber
     */
    public function __construct(
        Subscriber $subscriber,
        ModulesSettings $modulesSettings
    ) {
        $this->subscriber = $subscriber;
        $this->modulesSettings = $modulesSettings;
        $this->interests = $this->getInterests();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'email',
            EmailType::class,
            [
                'required' => true,
                'label' => 'lbl.Email',
                'attr' => [
                    'placeholder' => ucfirst(Language::lbl('YourEmail')),
                ],
            ]
        );

        if (!empty($this->interests)){
            $builder->add(
                'interests',
                ChoiceType::class,
                [
                    'choices' => $this->interests,
                    'expanded' => true,
                    'multiple' => true,
                ]
            );
        }

        $builder->add(
            'subscribe',
            SubmitType::class,
            [
                'label' => 'lbl.Subscribe',
            ]
        );
    }

    /**
     * @return array
     */
    public function getInterests()
    {
        $interests = array();

        try {
            $mailMotorInterests = $this->subscriber->getInterests();

            // Has interests
            if (!empty($mailMotorInterests)) {
                // Loop interests
                foreach ($mailMotorInterests as $categoryId => $categoryInterest) {
                    foreach ($categoryInterest['children'] as $categoryChildId => $categoryChildTitle) {
                        // Add interest value for checkbox
                        $interests[$categoryChildId] = $categoryChildTitle;
                    }
                }
            }
        // Fallback for when no mail-engine is chosen in the Backend
        } catch (NotImplementedException $e) {

        }

        return $interests;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
            'validation_groups' => function(FormInterface $form) {
                // Define overwrite interests
                $overwriteInterests = $this->modulesSettings->get('MailMotor', 'overwrite_interests', true);
                if (!empty($this->interests) && $overwriteInterests) {
                    return array('Default', 'has_interests');
                } else {
                    return array('Default');
                }
            },
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'subscribe';
    }
}
