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

namespace Sonata\UserBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Render a block with the connection option or the login name.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class AccountBlockService extends AbstractAdminBlockService
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(string $name, EngineInterface $templating, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($name, $templating);

        $this->tokenStorage = $tokenStorage;
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        $user = false;
        if ($this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();
        }
        if (!$user instanceof UserInterface) {
            $user = false;
        }

        return $this->renderPrivateResponse($blockContext->getTemplate(), [
            'user' => $user,
            'block' => $blockContext->getBlock(),
            'context' => $blockContext,
        ]);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => '@SonataUser/Block/account.html.twig',
            'ttl' => 0,
        ]);
    }

    public function getName(): string
    {
        return 'Account Block';
    }
}
