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

namespace Sonata\UserBundle\Mailer;

use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class Mailer implements MailerInterface
{
    private UrlGeneratorInterface $urlGenerator;

    private Environment $twig;

    private SymfonyMailerInterface $mailer;

    /**
     * @var array<string, string>
     */
    private array $fromEmail;

    private string $emailTemplate;

    /**
     * @param array<string, string> $fromEmail
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        Environment $twig,
        SymfonyMailerInterface $mailer,
        array $fromEmail,
        string $emailTemplate
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
        $this->emailTemplate = $emailTemplate;
    }

    public function sendResettingEmailMessage(UserInterface $user): void
    {
        $url = $this->urlGenerator->generate('sonata_user_admin_resetting_reset', [
            'token' => $user->getConfirmationToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $rendered = $this->twig->render($this->emailTemplate, [
            'user' => $user,
            'confirmationUrl' => $url,
        ]);

        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = preg_split('/\R/', trim($rendered), 2, \PREG_SPLIT_NO_EMPTY);
        \assert(false !== $renderedLines && [] !== $renderedLines);

        $subject = array_shift($renderedLines);
        $body = implode('', $renderedLines);
        $fromName = current($this->fromEmail);
        $fromAddress = current(array_keys($this->fromEmail));

        $this->mailer->send(
            (new Email())
                ->from(sprintf('%s <%s>', $fromName, $fromAddress))
                ->to((string) $user->getEmail())
                ->subject($subject)
                ->html($body)
        );
    }

    public function sendConfirmationEmailMessage(UserInterface $user): void
    {
        throw new \LogicException('This method is not implemented.');
    }
}
