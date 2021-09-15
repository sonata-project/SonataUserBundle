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

namespace Sonata\UserBundle\Tests\Controller\Api;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use PHPUnit\Framework\TestCase;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\UserBundle\Controller\Api\GroupController;
use Sonata\UserBundle\Entity\BaseGroup;
use Sonata\UserBundle\Model\GroupInterface;
use Sonata\UserBundle\Model\GroupManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class GroupControllerTest extends TestCase
{
    public function testGetGroupsAction(): void
    {
        $group = $this->createMock(GroupInterface::class);
        $groupManager = $this->createMock(GroupManagerInterface::class);
        $pager = $this->createStub(PagerInterface::class);
        $pager->method('getResults')->willReturn([$group]);
        $groupManager->expects(static::once())->method('getPager')->willReturn($pager);

        $paramFetcher = $this->createMock(ParamFetcherInterface::class);

        $paramFetcher->expects(static::exactly(3))->method('get')->willReturn(1, 10, null);
        $paramFetcher->expects(static::once())->method('all')->willReturn([]);

        static::assertSame([$group], $this->createGroupController(null, $groupManager)->getGroupsAction($paramFetcher)->getResults());
    }

    public function testGetGroupAction(): void
    {
        $group = $this->createMock(GroupInterface::class);
        static::assertSame($group, $this->createGroupController($group)->getGroupAction(1));
    }

    public function testGetGroupActionNotFoundException(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->createGroupController()->getGroupAction(42);
    }

    public function testPostGroupAction(): void
    {
        $group = $this->createMock(GroupInterface::class);

        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects(static::once())->method('getClass')->willReturn(BaseGroup::class);

        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($group);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createGroupController(null, $groupManager, $formFactory)->postGroupAction(new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPostGroupInvalidAction(): void
    {
        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects(static::once())->method('getClass')->willReturn(BaseGroup::class);

        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createGroupController(null, $groupManager, $formFactory)->postGroupAction(new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPutGroupAction(): void
    {
        $group = $this->createMock(GroupInterface::class);

        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects(static::once())->method('getClass')->willReturn(BaseGroup::class);
        $groupManager->expects(static::once())->method('findGroupBy')->willReturn($group);

        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($group);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createGroupController($group, $groupManager, $formFactory)->putGroupAction(1, new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPutGroupInvalidAction(): void
    {
        $group = $this->createMock(GroupInterface::class);

        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects(static::once())->method('getClass')->willReturn(BaseGroup::class);
        $groupManager->expects(static::once())->method('findGroupBy')->willReturn($group);

        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createGroupController($group, $groupManager, $formFactory)->putGroupAction(1, new Request());

        static::assertInstanceOf(FormInterface::class, $view->getData());
    }

    public function testDeleteGroupAction(): void
    {
        $group = $this->createMock(GroupInterface::class);

        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects(static::once())->method('findGroupBy')->willReturn($group);

        $view = $this->createGroupController($group, $groupManager)->deleteGroupAction(1);

        static::assertSame(['deleted' => true], $view->getData());
    }

    public function testDeleteGroupInvalidAction(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects(static::once())->method('findGroupBy')->willReturn(null);
        $groupManager->expects(static::never())->method('deleteGroup');

        $this->createGroupController(null, $groupManager)->deleteGroupAction(1);
    }

    public function createGroupController(
        ?GroupInterface $group = null,
        ?GroupManagerInterface $groupManager = null,
        ?FormFactoryInterface $formFactory = null
    ): GroupController {
        if (null === $groupManager) {
            $groupManager = $this->createMock(GroupManagerInterface::class);
        }
        if (null !== $group) {
            $groupManager->expects(static::once())->method('findGroupBy')->willReturn($group);
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock(FormFactoryInterface::class);
        }

        return new GroupController($groupManager, $formFactory);
    }
}
