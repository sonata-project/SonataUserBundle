<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Tests\DependencyInjection;

use FOS\UserBundle\Model\UserInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Mailer\Mailer;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class MailerTest extends TestCase
{
    /**
     * @var RouterInterface|MockObject
     */
    private $router;

    /**
     * @var Environment|MockObject
     */
    private $templating;

    /**
     * @var \Swift_Mailer|MockObject
     */
    private $mailer;

    /**
     * @var array
     */
    private $emailFrom;

    /**
     * @var string
     */
    private $template;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->templating = $this->createMock(Environment::class);
        $this->mailer = $this->createMock(\Swift_Mailer::class);
        $this->emailFrom = ['noreply@sonata-project.org'];
        $this->template = 'foo';
    }

    public function testSendConfirmationEmailMessage(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This method is not implemented.');

        $user = $this->createMock(UserInterface::class);

        $this->getMailer()->sendConfirmationEmailMessage($user);
    }

    /**
     * @dataProvider emailTemplateData
     */
    public function testSendResettingEmailMessage(string $template, string $subject, string $body): void
    {
        $user = $this->createMock(UserInterface::class);
        $user
            ->method('getConfirmationToken')
            ->willReturn('user-token');
        $user
            ->method('getEmail')
            ->willReturn('user@sonata-project.org');

        $this->router->expects($this->once())
            ->method('generate')
            ->with('sonata_user_admin_resetting_reset', ['token' => 'user-token'])
            ->willReturn('/foo');

        $this->templating->expects($this->once())
            ->method('render')
            ->with('foo', ['user' => $user, 'confirmationUrl' => '/foo'])
            ->willReturn($template);

        $this->mailer->expects($this->once())
            ->method('send')
            ->willReturnCallback(function (\Swift_Message $message) use ($subject, $body): void {
                $this->assertSame($subject, $message->getSubject());
                $this->assertSame($body, $message->getBody());
                $this->assertArrayHasKey($this->emailFrom[0], $message->getFrom());
                $this->assertArrayHasKey('user@sonata-project.org', $message->getTo());
            });

        $this->getMailer()->sendResettingEmailMessage($user);
    }

    public function emailTemplateData(): array
    {
        return [
            'CR' => ["Subject\rFirst line\rSecond line", 'Subject', "First line\rSecond line"],
            'LF' => ["Subject\nFirst line\nSecond line", 'Subject', "First line\nSecond line"],
            'CRLF' => ["Subject\r\nFirst line\r\nSecond line", 'Subject', "First line\r\nSecond line"],
            'LFLF' => ["Subject\n\nFirst line\n\nSecond line", 'Subject', "\nFirst line\n\nSecond line"],
            'CRCR' => ["Subject\r\rFirst line\r\rSecond line", 'Subject', "\rFirst line\r\rSecond line"],
        ];
    }

    private function getMailer(): Mailer
    {
        return new Mailer($this->router, $this->templating, $this->mailer, $this->emailFrom, $this->template);
    }
}
