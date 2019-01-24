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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CheckEmailActionTest extends TestCase
{
    /**
     * @var EngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templating;

    /**
     * @var UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlGenerator;

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

    public function setUp(): void
    {
        $this->templating = $this->createMock(EngineInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->pool = $this->createMock(Pool::class);
        $this->templateRegistry = $this->createMock(TemplateRegistryInterface::class);
        $this->resetTtl = 60;
    }

    public function testWithoutUsername(): void
    {
        $request = new Request();

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('sonata_user_admin_resetting_request')
            ->willReturn('/foo');

        $action = $this->getAction();
        $result = $action($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/foo', $result->getTargetUrl());
    }

    public function testWithUsername(): void
    {
        $request = new Request(['username' => 'bar']);
        $response = $this->createMock(Response::class);

        $parameters = [
            'base_template' => 'base.html.twig',
            'admin_pool' => $this->pool,
            'tokenLifetime' => 1,
        ];

        $this->templating->expects($this->once())
            ->method('renderResponse')
            ->with('@SonataUser/Admin/Security/Resetting/checkEmail.html.twig', $parameters)
            ->willReturn($response);

        $this->templateRegistry->expects($this->any())
            ->method('getTemplate')
            ->with('layout')
            ->willReturn('base.html.twig');

        $action = $this->getAction();
        $result = $action($request);

        $this->assertSame($response, $result);
    }

    private function getAction(): CheckEmailAction
    {
        return new CheckEmailAction($this->templating, $this->urlGenerator, $this->pool, $this->templateRegistry, $this->resetTtl);
    }
}
