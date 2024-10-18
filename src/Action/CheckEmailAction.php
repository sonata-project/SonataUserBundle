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

use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class CheckEmailAction
{
    private Pool $adminPool;
    private TemplateRegistryInterface $templateRegistry;
    private int $tokenTtl;

    /**
     * NEXT_MAJOR: Remove `$tokenTtlDeprecated` argument and only allow first types of arguments.
     */
    public function __construct(
        private Environment $twig,
        Pool|UrlGeneratorInterface $adminPool,
        TemplateRegistryInterface|Pool $templateRegistry,
        int|TemplateRegistryInterface $tokenTtl,
        ?int $tokenTtlDeprecated = null,
    ) {
        // NEXT_MAJOR: Remove all checks and use constructor property promotion instead
        if ($adminPool instanceof UrlGeneratorInterface) {
            if (!$templateRegistry instanceof Pool) {
                throw new \TypeError(\sprintf(
                    'Argument 3 passed to %s() must be an instance of %s, %s given.',
                    __METHOD__,
                    Pool::class,
                    $templateRegistry::class
                ));
            }
            $this->adminPool = $templateRegistry;

            if (!$tokenTtl instanceof TemplateRegistryInterface) {
                throw new \TypeError(\sprintf(
                    'Argument 4 passed to %s() must be an instance of %s, int given.',
                    __METHOD__,
                    TemplateRegistryInterface::class,
                ));
            }
            $this->templateRegistry = $tokenTtl;

            if (!\is_int($tokenTtlDeprecated)) {
                throw new \TypeError(\sprintf(
                    'Argument 5 passed to %s() must be type of %s, %s given.',
                    __METHOD__,
                    'integer',
                    \gettype($tokenTtlDeprecated)
                ));
            }
            $this->tokenTtl = $tokenTtlDeprecated;

            @trigger_error(\sprintf(
                'Passing an instance of %s as argument 2 to "%s()" is deprecated since sonata-project/user-bundle 5.13 and will only accept an instance of %s in version 6.0.',
                UrlGeneratorInterface::class,
                __METHOD__,
                Pool::class
            ), \E_USER_DEPRECATED);
        } else {
            $this->adminPool = $adminPool;
            if (!$templateRegistry instanceof TemplateRegistryInterface) {
                throw new \TypeError(\sprintf(
                    'Argument 3 passed to %s() must be an instance of %s, %s given.',
                    __METHOD__,
                    TemplateRegistryInterface::class,
                    $templateRegistry::class
                ));
            }
            $this->templateRegistry = $templateRegistry;

            if (!\is_int($tokenTtl)) {
                throw new \TypeError(\sprintf(
                    'Argument 4 passed to %s() must be type of %s, %s given.',
                    __METHOD__,
                    'integer',
                    \gettype($tokenTtl)
                ));
            }
            $this->tokenTtl = $tokenTtl;

            if (null !== $tokenTtlDeprecated) {
                throw new \TypeError(\sprintf(
                    'Argument 5 passed to %s() must be %s, %s given.',
                    __METHOD__,
                    'NULL',
                    \gettype($tokenTtlDeprecated)
                ));
            }
        }
    }

    public function __invoke(): Response
    {
        return new Response($this->twig->render('@SonataUser/Admin/Security/Resetting/checkEmail.html.twig', [
            'base_template' => $this->templateRegistry->getTemplate('layout'),
            'admin_pool' => $this->adminPool,
            'tokenLifetime' => ceil($this->tokenTtl / 3600),
        ]));
    }
}
