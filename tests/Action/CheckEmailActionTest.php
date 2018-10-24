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

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\UserBundle\Action\CheckEmailAction;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class CheckEmailActionTest extends TestCase
{
    /**
     * @var Pool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pool;

    /**
     * @var TemplateRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateRegistry;

    /**
     * @var int
     */
    protected $resetTtl;

    /**
     * @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    /**
     * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $router;

    /**
     * @var EngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templating;

    public function setUp(): void
    {
        $this->pool = $this->createMock(Pool::class);
        $this->templateRegistry = $this->createMock(TemplateRegistryInterface::class);
        $this->resetTtl = 60;
        $this->container = $this->createMock(ContainerBuilder::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->templating = $this->createMock(EngineInterface::class);

        $services = [
            'router' => $this->router,
            'templating' => $this->templating,
        ];
        $this->container->expects($this->any())
            ->method('has')
            ->willReturnCallback(function ($service) use ($services) {
                return isset($services[$service]);
            });
        $this->container->expects($this->any())
            ->method('get')
            ->willReturnCallback(function ($service) use ($services) {
                return $services[$service] ?? null;
            });
    }

    public function testWithoutUsername(): void
    {
        $request = new Request();

        $this->router->expects($this->once())
            ->method('generate')
            ->with('sonata_user_admin_resetting_request')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals('/foo', $result->getTargetUrl());
    }

    public function testWithUsername(): void
    {
        $request = new Request(['username' => 'bar']);

        $parameters = [
            'base_template' => 'base.html.twig',
            'admin_pool' => $this->pool,
            'tokenLifetime' => 1,
        ];

        $this->templating->expects($this->once())
            ->method('render')
            ->with('@SonataUser/Admin/Security/Resetting/checkEmail.html.twig', $parameters)
            ->willReturn('Foo Content');

        $this->templateRegistry->expects($this->any())
            ->method('getTemplate')
            ->with('layout')
            ->willReturn('base.html.twig');

        $action = $this->getAction();
        $result = $action($request);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals('Foo Content', $result->getContent());
    }

    private function getAction(): CheckEmailAction
    {
        $action = new CheckEmailAction($this->pool, $this->templateRegistry, $this->resetTtl);
        $action->setContainer($this->container);

        return $action;
    }
}
