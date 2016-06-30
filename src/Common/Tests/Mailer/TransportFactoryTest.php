<?php

namespace Common\Tests\Mailer;

use PHPUnit_Framework_TestCase;
use Common\Mailer\TransportFactory;

/**
 * Tests for our module settings
 */
class TransportFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreatesMailTransportByDefault()
    {
        $this->assertInstanceOf(
            'Swift_MailTransport',
            TransportFactory::create()
        );
    }

    public function testCreatesSmtpTransportIfWanted()
    {
        $this->assertInstanceOf(
            'Swift_SmtpTransport',
            TransportFactory::create('smtp')
        );
    }

    public function testEncryptionCanBeSet()
    {
        $transport = TransportFactory::create('smtp', null, null, null, null, 'ssl');
        $this->assertEquals(
            'ssl',
            $transport->getEncryption()
        );

        $transport = TransportFactory::create('smtp', null, null, null, null, 'tls');
        $this->assertEquals(
            'tls',
            $transport->getEncryption()
        );
    }
}
