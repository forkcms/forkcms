<?php

namespace Common\Tests\Mailer;

use Common\Mailer\Configurator;
use PHPUnit\Framework\TestCase;

/**
 * Tests for our module settings
 */
class ConfiguratorTest extends TestCase
{
    public function testConfiguratorSetsMailTransportByDefault()
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
                $this->equalTo('swiftmailer.mailer.default.transport'),
                $this->isInstanceOf('\Swift_MailTransport')
            )
        ;

        $configurator->onKernelRequest($this->getGetResponseEventMock());
    }

    public function testConfiguratorSetsSmtpTransport()
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
                $this->equalTo('swiftmailer.mailer.default.transport'),
                $this->isInstanceOf('\Swift_SmtpTransport')
            )
        ;

        $configurator->onKernelRequest($this->getGetResponseEventMock());
    }

    private function getModulesSettingsMock()
    {
        return $this->getMockBuilder('Common\ModulesSettings')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    private function getContainerMock()
    {
        return $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    private function getGetResponseEventMock()
    {
        return $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
