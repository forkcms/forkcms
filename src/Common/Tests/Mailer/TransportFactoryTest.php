<?php

namespace Common\Tests\Mailer;

use PHPUnit\Framework\TestCase;
use Common\Mailer\TransportFactory;

/**
 * Tests for our module settings
 */
class TransportFactoryTest extends TestCase
{
    public function testCreatesMailTransportByDefault()
    {
        self::assertInstanceOf(
            'Swift_MailTransport',
            TransportFactory::create()
        );
    }

    public function testCreatesSmtpTransportIfWanted()
    {
        self::assertInstanceOf(
            'Swift_SmtpTransport',
            TransportFactory::create('smtp')
        );
    }

    public function testEncryptionCanBeSet()
    {
        $transport = TransportFactory::create('smtp', null, null, null, null, 'ssl');
        self::assertEquals(
            'ssl',
            $transport->getEncryption()
        );

        $transport = TransportFactory::create('smtp', null, null, null, null, 'tls');
        self::assertEquals(
            'tls',
            $transport->getEncryption()
        );
    }
}
