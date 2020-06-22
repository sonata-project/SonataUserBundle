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
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\UserInterface;
use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Controller\Api\UserController;
use Sonata\UserBundle\Entity\BaseUser;
use Sonata\UserBundle\Model\GroupManagerInterface;
use Sonata\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class UserControllerTest extends TestCase
{
    public function testGetUsersAction(): void
    {
        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('getPager')->willReturn([]);

        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->willReturn([]);

        $this->assertSame([], $this->createUserController(null, $userManager)->getUsersAction($paramFetcher));
    }

    public function testGetUserAction(): void
    {
        $user = $this->createMock(\Sonata\UserBundle\Model\UserInterface::class);
        $this->assertSame($user, $this->createUserController($user)->getUserAction(1));
    }

    public function testGetUserActionNotFoundException(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('User (42) not found');

        $this->createUserController()->getUserAction(42);
    }

    public function testPostUserAction(): void
    {
        $user = $this->createMock(UserInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('updateUser')->willReturn($user);

        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(true);
        $form->expects($this->once())->method('getData')->willReturn($user);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createUserController(null, $userManager, null, $formFactory)->postUserAction(new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPostUserInvalidAction(): void
    {
        $userManager = $this->createMock(UserManagerInterface::class);

        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(false);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createUserController(null, $userManager, null, $formFactory)->postUserAction(new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutUserAction(): void
    {
        $user = $this->createMock(UserInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('findUserBy')->willReturn($user);
        $userManager->expects($this->once())->method('updateUser')->willReturn($user);

        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(true);
        $form->expects($this->once())->method('getData')->willReturn($user);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createUserController($user, $userManager, null, $formFactory)->putUserAction(1, new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPutUserInvalidAction(): void
    {
        $user = $this->createMock(UserInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('findUserBy')->willReturn($user);

        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(false);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createUserController($user, $userManager, null, $formFactory)->putUserAction(1, new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testPostUserGroupAction(): void
    {
        $user = $this->createMock(BaseUser::class);
        $user->expects($this->once())->method('hasGroup')->willReturn(false);

        $group = $this->createMock(GroupInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('findUserBy')->willReturn($user);
        $userManager->expects($this->once())->method('updateUser')->willReturn($user);

        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects($this->once())->method('findGroupBy')->willReturn($group);

        $view = $this->createUserController($user, $userManager, $groupManager)->postUserGroupAction(1, 1);

        $this->assertSame(['added' => true], $view);
    }

    public function testPostUserGroupInvalidAction(): void
    {
        $user = $this->createMock(BaseUser::class);
        $user->expects($this->once())->method('hasGroup')->willReturn(true);

        $group = $this->createMock(GroupInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('findUserBy')->willReturn($user);

        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects($this->once())->method('findGroupBy')->willReturn($group);

        $view = $this->createUserController($user, $userManager, $groupManager)->postUserGroupAction(1, 1);

        $this->assertInstanceOf(View::class, $view);
        $this->assertSame(400, $view->getStatusCode(), 'Should return 400');

        $data = $view->getData();

        $this->assertSame(['error' => 'User "1" already has group "1"'], $data);
    }

    public function testDeleteUserGroupAction(): void
    {
        $user = $this->createMock(BaseUser::class);
        $user->expects($this->once())->method('hasGroup')->willReturn(true);

        $group = $this->createMock(GroupInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('findUserBy')->willReturn($user);
        $userManager->expects($this->once())->method('updateUser')->willReturn($user);

        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects($this->once())->method('findGroupBy')->willReturn($group);

        $view = $this->createUserController($user, $userManager, $groupManager)->deleteUserGroupAction(1, 1);

        $this->assertSame(['removed' => true], $view);
    }

    public function testDeleteUserGroupInvalidAction(): void
    {
        $user = $this->createMock(BaseUser::class);
        $user->expects($this->once())->method('hasGroup')->willReturn(false);

        $group = $this->createMock(GroupInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('findUserBy')->willReturn($user);

        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects($this->once())->method('findGroupBy')->willReturn($group);

        $view = $this->createUserController($user, $userManager, $groupManager)->deleteUserGroupAction(1, 1);

        $this->assertInstanceOf(View::class, $view);
        $this->assertSame(400, $view->getStatusCode(), 'Should return 400');

        $data = $view->getData();

        $this->assertSame(['error' => 'User "1" has not group "1"'], $data);
    }

    public function testDeleteUserAction(): void
    {
        $user = $this->createMock(UserInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('findUserBy')->willReturn($user);
        $userManager->expects($this->once())->method('deleteUser')->willReturn($user);

        $view = $this->createUserController($user, $userManager)->deleteUserAction(1);

        $this->assertSame(['deleted' => true], $view);
    }

    public function testDeleteUserInvalidAction(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('findUserBy')->willReturn(null);
        $userManager->expects($this->never())->method('deleteUser');

        $this->createUserController(null, $userManager)->deleteUserAction(1);
    }

    /**
     * @param $user
     * @param $userManager
     * @param $groupManager
     * @param $formFactory
     */
    public function createUserController(
        ?UserInterface $user = null,
        ?UserManagerInterface $userManager = null,
        ?GroupManagerInterface $groupManager = null,
        ?FormFactoryInterface $formFactory = null
    ): UserController {
        if (null === $userManager) {
            $userManager = $this->createMock(UserManagerInterface::class);
        }
        if (null === $groupManager) {
            $groupManager = $this->createMock(GroupManagerInterface::class);
        }
        if (null !== $user) {
            $userManager->expects($this->once())->method('findUserBy')->willReturn($user);
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock(FormFactoryInterface::class);
        }

        return new UserController($userManager, $groupManager, $formFactory);
    }
}
