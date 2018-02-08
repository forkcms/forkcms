<?php

namespace App\Tests\Service\Mailer;

use App\Service\Mailer\Configurator;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Tests for our module settings
 */
class ConfiguratorTest extends TestCase
{
    public function testConfiguratorSetsMailTransportByDefault(): void
    {
        $moduleSettingsMock = $this->getModuleSettingsMock();
        $containerMock =
            $this->getContainerMock();

        $configurator = new Configurator(
            $moduleSettingsMock,
            $containerMock
        );

        // always return null: we have no modules settings set
        $moduleSettingsMock
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
        $moduleSettingsMock = $this->getModuleSettingsMock();
        $containerMock =
            $this->getContainerMock();

        $configurator = new Configurator(
            $moduleSettingsMock,
            $containerMock
        );

        // always return null: we have modules settings set for smtp
        $moduleSettingsMock
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

    private function getModuleSettingsMock(): PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder('App\Service\Module\ModuleSettings')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    private function getContainerMock(): PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    private function getGetResponseEventMock(): PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
