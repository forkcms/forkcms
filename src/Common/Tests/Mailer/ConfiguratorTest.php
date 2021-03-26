<?php

namespace Common\Tests\Mailer;

use Common\Mailer\Configurator;
use Common\ModulesSettings;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Tests for our module settings
 */
class ConfiguratorTest extends TestCase
{
    public function testConfiguratorSetsMailTransportByDefault(): void
    {
        $modulesSettingsMock = $this->getModulesSettingsMock();
        $containerMock =
            $this->getContainerMock();

        $configurator = new Configurator(
            $modulesSettingsMock,
            $containerMock
        );

        // always return null: we have no modules settings set
        $modulesSettingsMock
            ->expects($this->exactly(6))
            ->method('get')
            ->will($this->returnValue(null))
        ;

        // we want our set method to be called with a Mail transport
        $containerMock
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo('swiftmailer.transport'),
                $this->isInstanceOf('\Swift_SendmailTransport')
            )
        ;

        $configurator->onKernelRequest($this->getGetResponseEventMock());
    }

    public function testConfiguratorSetsSmtpTransport(): void
    {
        $modulesSettingsMock = $this->getModulesSettingsMock();
        $containerMock =
            $this->getContainerMock();

        $configurator = new Configurator(
            $modulesSettingsMock,
            $containerMock
        );

        // always return null: we have modules settings set for smtp
        $modulesSettingsMock
            ->expects($this->exactly(6))
            ->method('get')
            ->will($this->onConsecutiveCalls(
                'smtp',
                'test.server.com',
                25,
                'test@server.com',
                'testpass'
            ))
        ;

        // we want our set method to be called with a Smtp transport
        $containerMock
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo('swiftmailer.transport'),
                $this->isInstanceOf('\Swift_SmtpTransport')
            )
        ;

        $configurator->onKernelRequest($this->getGetResponseEventMock());
    }

    private function getModulesSettingsMock(): ModulesSettings
    {
        return $this->createMock(ModulesSettings::class);
    }

    private function getContainerMock(): ContainerInterface
    {
        return $this->createMock(ContainerInterface::class);
    }

    private function getGetResponseEventMock(): GetResponseEvent
    {
        return $this->createMock(GetResponseEvent::class);
    }
}
