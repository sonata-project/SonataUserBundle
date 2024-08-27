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

namespace Sonata\UserBundle\Tests\Action;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\UserBundle\Action\CheckEmailAction;
use Symfony\Component\DependencyInjection\Container;
use Twig\Environment;

final class CheckEmailActionTest extends TestCase
{
    /**
     * @var MockObject&Environment
     */
    private MockObject $templating;

    private Pool $pool;

    /**
     * @var MockObject&TemplateRegistryInterface
     */
    private MockObject $templateRegistry;

    private int $resetTtl;

    protected function setUp(): void
    {
        $this->templating = $this->createMock(Environment::class);
        $this->pool = new Pool(new Container());
        $this->templateRegistry = $this->createMock(TemplateRegistryInterface::class);
        $this->resetTtl = 60;
    }

    public function testWithUsername(): void
    {
        $parameters = [
            'base_template' => 'base.html.twig',
            'admin_pool' => $this->pool,
            'tokenLifetime' => 1,
        ];

        $this->templating->expects(static::once())
            ->method('render')
            ->with('@SonataUser/Admin/Security/Resetting/checkEmail.html.twig', $parameters)
            ->willReturn('template content');

        $this->templateRegistry
            ->method('getTemplate')
            ->with('layout')
            ->willReturn('base.html.twig');

        $action = $this->getAction();
        $result = $action();

        static::assertSame('template content', $result->getContent());
    }

    private function getAction(): CheckEmailAction
    {
        return new CheckEmailAction($this->templating, $this->pool, $this->templateRegistry, $this->resetTtl);
    }
}
