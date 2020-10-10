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

use FOS\UserBundle\Mailer\MailerInterface;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class Mailer implements MailerInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var array
     */
    private $fromEmail;

    /**
     * @var string
     */
    private $emailTemplate;

    public function __construct(UrlGeneratorInterface $urlGenerator, Environment $twig, \Swift_Mailer $mailer, array $fromEmail, string $emailTemplate)
    {
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
        $renderedLines = preg_split('/\R/', trim($rendered), 2, PREG_SPLIT_NO_EMPTY);
        $subject = array_shift($renderedLines);
        $body = implode('', $renderedLines);
        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($this->fromEmail)
            ->setTo((string) $user->getEmail())
            ->setBody($body);

        $this->mailer->send($message);
    }

    public function sendConfirmationEmailMessage(UserInterface $user): void
    {
        throw new \LogicException('This method is not implemented.');
    }
}
