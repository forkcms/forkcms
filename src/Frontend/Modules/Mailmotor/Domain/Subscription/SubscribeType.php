<?php

namespace Frontend\Modules\Mailmotor\Domain\Subscription;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\ModulesSettings;
use DateTime;
use Frontend\Core\Engine\Navigation;
use Frontend\Modules\Mailmotor\Domain\Subscription\Command\Subscription;
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

    public function __construct(
        Subscriber $subscriber,
        ModulesSettings $modulesSettings
    ) {
        $this->subscriber = $subscriber;
        $this->modulesSettings = $modulesSettings;
        $this->interests = $this->getInterests();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //-- Set the default submit action, this is for the widget to work properly.
        $builder->setAction(Navigation::getUrlForBlock('Mailmotor', 'Subscribe'));

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

        if (!empty($this->interests)) {
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

    public function getInterests(): array
    {
        $interests = [];

        // should we be checking interests (CampaignMonitor for example has no interests)
        $mailMotorInterestsCheckInterests = $this->modulesSettings->get('Mailmotor', 'check_interests', true);
        if (!$mailMotorInterestsCheckInterests) {
            return [];
        }

        try {
            $mailMotorInterests = $this->modulesSettings->get('Mailmotor', 'interests');
            $mailMotorInterestsLastChecked = $this->modulesSettings->get('Mailmotor', 'interests_last_checked');

            // get cached interests
            if (is_array($mailMotorInterests)
                && $mailMotorInterestsLastChecked instanceof DateTime
                && $mailMotorInterestsLastChecked > new DateTime('-8 hours')
            ) {
                return $mailMotorInterests;
            }

            $mailMotorInterests = $this->subscriber->getInterests();

            // Has interests
            if (empty($mailMotorInterests) || !is_array($mailMotorInterests)) {
                return $interests;
            }

            // Loop interests
            foreach ($mailMotorInterests as $categoryId => $categoryInterest) {
                if (empty($categoryInterest['children']) || !is_array($categoryInterest['children'])) {
                    continue;
                }

                foreach ($categoryInterest['children'] as $categoryChildId => $categoryChildTitle) {
                    // Add interest value for checkbox
                    $interests[$categoryChildTitle] = $categoryChildId;
                }
            }
            $this->modulesSettings->set('Mailmotor', 'interests', $interests);
            $this->modulesSettings->set('Mailmotor', 'interests_last_checked', new DateTime());
        } catch (NotImplementedException $e) {
            // Fallback for when no mail-engine is chosen in the Backend
            $this->modulesSettings->set('Mailmotor', 'check_interests', false);

            return [];
        }

        return $interests;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
            'validation_groups' => function (FormInterface $form) {
                // Define overwrite interests
                $overwriteInterests = $this->modulesSettings->get('Mailmotor', 'overwrite_interests', true);
                if (!empty($this->interests) && $overwriteInterests) {
                    return ['Default', 'has_interests'];
                }

                return ['Default'];
            },
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'subscribe';
    }
}
