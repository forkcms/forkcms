<?php

namespace Backend\Modules\MailMotor\Factory;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class MailMotorFactory
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $mailEngine;

    /**
     * Construct
     *
     * @param Container $container
     * @param string $mailEngine
     */
    public function __construct(
        $container,
        $mailEngine
    ) {
        $this->container = $container;
        $this->setMailEngine($mailEngine);
    }

    /**
     * Get subscriber gateway
     *
     * @return Gateway
     */
    public function getSubscriberGateway()
    {
        return $this->container->get('mailmotor.' . $this->mailEngine . '.subscriber.gateway');
    }

    /**
     * Set mail engine
     *
     * @param string $mailEngine
     */
    protected function setMailEngine($mailEngine)
    {
        if ($mailEngine == null) {
            $mailEngine = 'not_implemented';
        }

        $this->mailEngine = strtolower($mailEngine);
    }
}
