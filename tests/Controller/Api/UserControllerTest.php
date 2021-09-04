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
use Sonata\DatagridBundle\Pager\PagerInterface;
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
        $user = $this->createMock(UserInterface::class);
        $userManager = $this->createMock(UserManagerInterface::class);
        $pager = $this->createStub(PagerInterface::class);
        $pager->method('getResults')->willReturn([$user]);
        $userManager->expects(static::once())->method('getPager')->willReturn($pager);

        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects(static::exactly(3))->method('get')->willReturn(1, 10, null);
        $paramFetcher->expects(static::once())->method('all')->willReturn([]);

        static::assertSame([$user], $this->createUserController(null, $userManager)->getUsersAction($paramFetcher)->getResults());
    }

    public function testGetUserAction(): void
    {
        $user = $this->createStub(UserInterface::class);
        static::assertSame($user, $this->createUserController($user)->getUserAction(1));
    }

    /**
     * @dataProvider getIdsForNotFound
     */
    public function testGetUserActionNotFoundException($identifier, string $message): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage($message);

        $this->createUserController()->getUserAction($identifier);
    }

    /**
     * @phpstan-return list<array{mixed, string}>
     */
    public function getIdsForNotFound(): array
    {
        return [
            [42, 'User not found for identifier 42.'],
            ['42', 'User not found for identifier \'42\'.'],
            [null, 'User not found for identifier NULL.'],
            ['', 'User not found for identifier \'\'.'],
        ];
    }

    public function testPostUserAction(): void
    {
        $user = $this->createMock(UserInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects(static::once())->method('updateUser')->willReturn($user);

        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($user);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createUserController(null, $userManager, null, $formFactory)->postUserAction(new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPostUserInvalidAction(): void
    {
        $userManager = $this->createMock(UserManagerInterface::class);

        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createUserController(null, $userManager, null, $formFactory)->postUserAction(new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutUserAction(): void
    {
        $user = $this->createMock(UserInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects(static::once())->method('findUserBy')->willReturn($user);
        $userManager->expects(static::once())->method('updateUser')->willReturn($user);

        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($user);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createUserController($user, $userManager, null, $formFactory)->putUserAction(1, new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPutUserInvalidAction(): void
    {
        $user = $this->createMock(UserInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects(static::once())->method('findUserBy')->willReturn($user);

        $form = $this->getMockBuilder(Form::class)->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createUserController($user, $userManager, null, $formFactory)->putUserAction(1, new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testPostUserGroupAction(): void
    {
        $user = $this->createMock(BaseUser::class);
        $user->expects(static::once())->method('hasGroup')->willReturn(false);

        $group = $this->createMock(GroupInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects(static::once())->method('findUserBy')->willReturn($user);
        $userManager->expects(static::once())->method('updateUser')->willReturn($user);

        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects(static::once())->method('findGroupBy')->willReturn($group);

        $view = $this->createUserController($user, $userManager, $groupManager)->postUserGroupAction(1, 1);

        static::assertSame(['added' => true], $view);
    }

    public function testPostUserGroupInvalidAction(): void
    {
        $user = $this->createMock(BaseUser::class);
        $user->expects(static::once())->method('hasGroup')->willReturn(true);

        $group = $this->createMock(GroupInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects(static::once())->method('findUserBy')->willReturn($user);

        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects(static::once())->method('findGroupBy')->willReturn($group);

        $view = $this->createUserController($user, $userManager, $groupManager)->postUserGroupAction(1, 1);

        static::assertInstanceOf(View::class, $view);
        static::assertSame(400, $view->getStatusCode(), 'Should return 400');

        $data = $view->getData();

        static::assertSame(['error' => 'User "1" already has group "1"'], $data);
    }

    public function testDeleteUserGroupAction(): void
    {
        $user = $this->createMock(BaseUser::class);
        $user->expects(static::once())->method('hasGroup')->willReturn(true);

        $group = $this->createMock(GroupInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects(static::once())->method('findUserBy')->willReturn($user);
        $userManager->expects(static::once())->method('updateUser')->willReturn($user);

        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects(static::once())->method('findGroupBy')->willReturn($group);

        $view = $this->createUserController($user, $userManager, $groupManager)->deleteUserGroupAction(1, 1);

        static::assertSame(['removed' => true], $view);
    }

    public function testDeleteUserGroupInvalidAction(): void
    {
        $user = $this->createMock(BaseUser::class);
        $user->expects(static::once())->method('hasGroup')->willReturn(false);

        $group = $this->createMock(GroupInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects(static::once())->method('findUserBy')->willReturn($user);

        $groupManager = $this->createMock(GroupManagerInterface::class);
        $groupManager->expects(static::once())->method('findGroupBy')->willReturn($group);

        $view = $this->createUserController($user, $userManager, $groupManager)->deleteUserGroupAction(1, 1);

        static::assertInstanceOf(View::class, $view);
        static::assertSame(400, $view->getStatusCode(), 'Should return 400');

        $data = $view->getData();

        static::assertSame(['error' => 'User "1" has not group "1"'], $data);
    }

    public function testDeleteUserAction(): void
    {
        $user = $this->createMock(UserInterface::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects(static::once())->method('findUserBy')->willReturn($user);
        $userManager->expects(static::once())->method('deleteUser')->willReturn($user);

        $view = $this->createUserController($user, $userManager)->deleteUserAction(1);

        static::assertSame(['deleted' => true], $view);
    }

    public function testDeleteUserInvalidAction(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects(static::once())->method('findUserBy')->willReturn(null);
        $userManager->expects(static::never())->method('deleteUser');

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
            $userManager->expects(static::once())->method('findUserBy')->willReturn($user);
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock(FormFactoryInterface::class);
        }

        return new UserController($userManager, $groupManager, $formFactory);
    }
}
