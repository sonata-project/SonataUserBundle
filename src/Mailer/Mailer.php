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
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Email;
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
     * NEXT_MAJOR: Remove the support for `\Swift_Mailer` in this property.
     *
     * @var SymfonyMailerInterface|\Swift_Mailer
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

    public function __construct(UrlGeneratorInterface $urlGenerator, Environment $twig, object $mailer, array $fromEmail, string $emailTemplate)
    {
        // NEXT_MAJOR: Remove the following 2 conditions and use `Symfony\Component\Mailer\MailerInterface` as argument declaration for `$mailer`.
        if (!$mailer instanceof SymfonyMailerInterface && !$mailer instanceof \Swift_Mailer) {
            throw new \TypeError(sprintf(
                'Argument 3 passed to "%s()" must be an instance of "%s" or "%s", instance of "%s" given.',
                __METHOD__,
                SymfonyMailerInterface::class,
                \Swift_Mailer::class,
                \get_class($mailer)
            ));
        }

        if (!$mailer instanceof SymfonyMailerInterface) {
            @trigger_error(sprintf(
                'Passing other type than "%s" as argument 3 for "%s()" is deprecated since sonata-project/user-bundle 4.10'
                .' and will be not supported in version 5.0.',
                SymfonyMailerInterface::class,
                __METHOD__
            ), \E_USER_DEPRECATED);
        }

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
        $subject = array_shift($renderedLines);
        $body = implode('', $renderedLines);

        // NEXT_MAJOR: Remove this condition.
        if ($this->mailer instanceof \Swift_Mailer) {
            $this->sendResettingEmailMessageWithSwiftMailer($user, $subject, $body);

            return;
        }

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

    /**
     * NEXT_MAJOR: Remove this method.
     */
    private function sendResettingEmailMessageWithSwiftMailer(UserInterface $user, string $subject, string $body): void
    {
        $this->mailer->send(
            (new \Swift_Message())
                ->setSubject($subject)
                ->setFrom($this->fromEmail)
                ->setTo((string) $user->getEmail())
                ->setBody($body)
        );
    }
}
