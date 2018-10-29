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

namespace Sonata\UserBundle\Action;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SendEmailAction
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Pool
     */
    private $adminPool;

    /**
     * @var TemplateRegistryInterface
     */
    private $templateRegistry;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var int
     */
    private $resetTtl;

    /**
     * @var string[]
     */
    private $fromEmail;

    /**
     * @var string
     */
    private $template;

    public function __construct(
        EngineInterface $templating,
        UrlGeneratorInterface $urlGenerator,
        Pool $adminPool,
        TemplateRegistryInterface $templateRegistry,
        UserManagerInterface $userManager,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $tokenGenerator,
        int $resetTtl,
        array $fromEmail,
        string $template
    ) {
        $this->templating = $templating;
        $this->urlGenerator = $urlGenerator;
        $this->adminPool = $adminPool;
        $this->templateRegistry = $templateRegistry;
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->resetTtl = $resetTtl;
        $this->fromEmail = $fromEmail;
        $this->template = $template;
    }

    public function __invoke(Request $request): Response
    {
        $username = $request->request->get('username');

        $user = $this->userManager->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return $this->templating->renderResponse('@SonataUser/Admin/Security/Resetting/request.html.twig', [
                'base_template' => $this->templateRegistry->getTemplate('layout'),
                'admin_pool' => $this->adminPool,
                'invalid_username' => $username,
            ]);
        }

        if (null !== $user && !$user->isPasswordRequestNonExpired($this->resetTtl)) {
            if (!$user->isAccountNonLocked()) {
                return new RedirectResponse($this->urlGenerator->generate('sonata_user_admin_resetting_request'));
            }

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
            }

            $this->sendResettingEmailMessage($user);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->userManager->updateUser($user);
        }

        return new RedirectResponse($this->urlGenerator->generate('sonata_user_admin_resetting_check_email', [
            'username' => $username,
        ]));
    }

    private function sendResettingEmailMessage(UserInterface $user): void
    {
        $url = $this->urlGenerator->generate('sonata_user_admin_resetting_reset', [
            'token' => $user->getConfirmationToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $rendered = $this->templating->render($this->template, [
            'user' => $user,
            'confirmationUrl' => $url,
        ]);

        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode(PHP_EOL, trim($rendered));
        $subject = array_shift($renderedLines);
        $body = implode(PHP_EOL, $renderedLines);
        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($this->fromEmail)
            ->setTo((string) $user->getEmail())
            ->setBody($body);

        $this->mailer->send($message);
    }
}
