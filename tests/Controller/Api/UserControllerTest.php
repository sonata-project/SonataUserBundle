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

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Controller\Api\UserController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class UserControllerTest extends TestCase
{
    public function testGetUsersAction(): void
    {
        $userManager = $this->createMock('Sonata\UserBundle\Model\UserManagerInterface');
        $userManager->expects($this->once())->method('getPager')->will($this->returnValue([]));

        $paramFetcher = $this->getMockBuilder('FOS\RestBundle\Request\ParamFetcher')
            ->disableOriginalConstructor()
            ->getMock();
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));

        $this->assertSame([], $this->createUserController(null, $userManager)->getUsersAction($paramFetcher));
    }

    public function testGetUserAction(): void
    {
        $user = $this->createMock('Sonata\UserBundle\Model\UserInterface');
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
        $user = $this->createMock('FOS\UserBundle\Model\UserInterface');

        $userManager = $this->createMock('Sonata\UserBundle\Model\UserManagerInterface');
        $userManager->expects($this->once())->method('updateUser')->will($this->returnValue($user));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($user));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createUserController(null, $userManager, null, $formFactory)->postUserAction(new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostUserInvalidAction(): void
    {
        $userManager = $this->createMock('Sonata\UserBundle\Model\UserManagerInterface');

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createUserController(null, $userManager, null, $formFactory)->postUserAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutUserAction(): void
    {
        $user = $this->createMock('FOS\UserBundle\Model\UserInterface');

        $userManager = $this->createMock('Sonata\UserBundle\Model\UserManagerInterface');
        $userManager->expects($this->once())->method('findUserBy')->will($this->returnValue($user));
        $userManager->expects($this->once())->method('updateUser')->will($this->returnValue($user));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($user));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createUserController($user, $userManager, null, $formFactory)->putUserAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutUserInvalidAction(): void
    {
        $user = $this->createMock('FOS\UserBundle\Model\UserInterface');

        $userManager = $this->createMock('Sonata\UserBundle\Model\UserManagerInterface');
        $userManager->expects($this->once())->method('findUserBy')->will($this->returnValue($user));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createUserController($user, $userManager, null, $formFactory)->putUserAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPostUserGroupAction(): void
    {
        $user = $this->createMock('Sonata\UserBundle\Entity\BaseUser');
        $user->expects($this->once())->method('hasGroup')->will($this->returnValue(false));

        $group = $this->createMock('FOS\UserBundle\Model\GroupInterface');

        $userManager = $this->createMock('Sonata\UserBundle\Model\UserManagerInterface');
        $userManager->expects($this->once())->method('findUserBy')->will($this->returnValue($user));
        $userManager->expects($this->once())->method('updateUser')->will($this->returnValue($user));

        $groupManager = $this->createMock('Sonata\UserBundle\Model\GroupManagerInterface');
        $groupManager->expects($this->once())->method('findGroupBy')->will($this->returnValue($group));

        $view = $this->createUserController($user, $userManager, $groupManager)->postUserGroupAction(1, 1);

        $this->assertSame(['added' => true], $view);
    }

    public function testPostUserGroupInvalidAction(): void
    {
        $user = $this->createMock('Sonata\UserBundle\Entity\BaseUser');
        $user->expects($this->once())->method('hasGroup')->will($this->returnValue(true));

        $group = $this->createMock('FOS\UserBundle\Model\GroupInterface');

        $userManager = $this->createMock('Sonata\UserBundle\Model\UserManagerInterface');
        $userManager->expects($this->once())->method('findUserBy')->will($this->returnValue($user));

        $groupManager = $this->createMock('Sonata\UserBundle\Model\GroupManagerInterface');
        $groupManager->expects($this->once())->method('findGroupBy')->will($this->returnValue($group));

        $view = $this->createUserController($user, $userManager, $groupManager)->postUserGroupAction(1, 1);

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
        $this->assertSame(400, $view->getStatusCode(), 'Should return 400');

        $data = $view->getData();

        $this->assertSame(['error' => 'User "1" already has group "1"'], $data);
    }

    public function testDeleteUserGroupAction(): void
    {
        $user = $this->createMock('Sonata\UserBundle\Entity\BaseUser');
        $user->expects($this->once())->method('hasGroup')->will($this->returnValue(true));

        $group = $this->createMock('FOS\UserBundle\Model\GroupInterface');

        $userManager = $this->createMock('Sonata\UserBundle\Model\UserManagerInterface');
        $userManager->expects($this->once())->method('findUserBy')->will($this->returnValue($user));
        $userManager->expects($this->once())->method('updateUser')->will($this->returnValue($user));

        $groupManager = $this->createMock('Sonata\UserBundle\Model\GroupManagerInterface');
        $groupManager->expects($this->once())->method('findGroupBy')->will($this->returnValue($group));

        $view = $this->createUserController($user, $userManager, $groupManager)->deleteUserGroupAction(1, 1);

        $this->assertSame(['removed' => true], $view);
    }

    public function testDeleteUserGroupInvalidAction(): void
    {
        $user = $this->createMock('Sonata\UserBundle\Entity\BaseUser');
        $user->expects($this->once())->method('hasGroup')->will($this->returnValue(false));

        $group = $this->createMock('FOS\UserBundle\Model\GroupInterface');

        $userManager = $this->createMock('Sonata\UserBundle\Model\UserManagerInterface');
        $userManager->expects($this->once())->method('findUserBy')->will($this->returnValue($user));

        $groupManager = $this->createMock('Sonata\UserBundle\Model\GroupManagerInterface');
        $groupManager->expects($this->once())->method('findGroupBy')->will($this->returnValue($group));

        $view = $this->createUserController($user, $userManager, $groupManager)->deleteUserGroupAction(1, 1);

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
        $this->assertSame(400, $view->getStatusCode(), 'Should return 400');

        $data = $view->getData();

        $this->assertSame(['error' => 'User "1" has not group "1"'], $data);
    }

    public function testDeleteUserAction(): void
    {
        $user = $this->createMock('FOS\UserBundle\Model\UserInterface');

        $userManager = $this->createMock('Sonata\UserBundle\Model\UserManagerInterface');
        $userManager->expects($this->once())->method('findUserBy')->will($this->returnValue($user));
        $userManager->expects($this->once())->method('deleteUser')->will($this->returnValue($user));

        $view = $this->createUserController($user, $userManager)->deleteUserAction(1);

        $this->assertSame(['deleted' => true], $view);
    }

    public function testDeleteUserInvalidAction(): void
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $userManager = $this->createMock('Sonata\UserBundle\Model\UserManagerInterface');
        $userManager->expects($this->once())->method('findUserBy')->will($this->returnValue(null));
        $userManager->expects($this->never())->method('deleteUser');

        $this->createUserController(null, $userManager)->deleteUserAction(1);
    }

    /**
     * @param $user
     * @param $userManager
     * @param $groupManager
     * @param $formFactory
     *
     * @return UserController
     */
    public function createUserController($user = null, $userManager = null, $groupManager = null, $formFactory = null)
    {
        if (null === $userManager) {
            $userManager = $this->createMock('Sonata\UserBundle\Model\UserManagerInterface');
        }
        if (null === $groupManager) {
            $groupManager = $this->createMock('Sonata\UserBundle\Model\GroupManagerInterface');
        }
        if (null !== $user) {
            $userManager->expects($this->once())->method('findUserBy')->will($this->returnValue($user));
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        }

        return new UserController($userManager, $groupManager, $formFactory);
    }
}
