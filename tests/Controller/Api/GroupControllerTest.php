<?php

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
use Sonata\UserBundle\Controller\Api\GroupController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class GroupControllerTest extends TestCase
{
    public function testGetGroupsAction()
    {
        $group = $this->createMock('FOS\UserBundle\Model\GroupInterface');
        $groupManager = $this->createMock('Sonata\UserBundle\Model\GroupManagerInterface');
        $groupManager->expects($this->once())->method('getPager')->will($this->returnValue([$group]));

        $paramFetcher = $this->getMockBuilder('FOS\RestBundle\Request\ParamFetcher')
            ->disableOriginalConstructor()
            ->getMock();
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));

        $this->assertEquals([$group], $this->createGroupController(null, $groupManager)->getGroupsAction($paramFetcher));
    }

    public function testGetGroupAction()
    {
        $group = $this->createMock('FOS\UserBundle\Model\GroupInterface');
        $this->assertEquals($group, $this->createGroupController($group)->getGroupAction(1));
    }

    public function testGetGroupActionNotFoundException()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('Group (42) not found');

        $this->createGroupController()->getGroupAction(42);
    }

    public function testPostGroupAction()
    {
        $group = $this->createMock('FOS\UserBundle\Model\GroupInterface');

        $groupManager = $this->createMock('Sonata\UserBundle\Model\GroupManagerInterface');
        $groupManager->expects($this->once())->method('getClass')->will($this->returnValue('FOS\UserBundle\Entity\Group'));
        $groupManager->expects($this->once())->method('updateGroup')->will($this->returnValue($group));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('submit');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($group));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createGroupController(null, $groupManager, $formFactory)->postGroupAction(new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostGroupInvalidAction()
    {
        $groupManager = $this->createMock('Sonata\UserBundle\Model\GroupManagerInterface');
        $groupManager->expects($this->once())->method('getClass')->will($this->returnValue('FOS\UserBundle\Entity\Group'));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('submit');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createGroupController(null, $groupManager, $formFactory)->postGroupAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutGroupAction()
    {
        $group = $this->createMock('FOS\UserBundle\Model\GroupInterface');

        $groupManager = $this->createMock('Sonata\UserBundle\Model\GroupManagerInterface');
        $groupManager->expects($this->once())->method('getClass')->will($this->returnValue('FOS\UserBundle\Entity\Group'));
        $groupManager->expects($this->once())->method('findGroupBy')->will($this->returnValue($group));
        $groupManager->expects($this->once())->method('updateGroup')->will($this->returnValue($group));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('submit');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($group));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createGroupController($group, $groupManager, $formFactory)->putGroupAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutGroupInvalidAction()
    {
        $group = $this->createMock('FOS\UserBundle\Model\GroupInterface');

        $groupManager = $this->createMock('Sonata\UserBundle\Model\GroupManagerInterface');
        $groupManager->expects($this->once())->method('getClass')->will($this->returnValue('FOS\UserBundle\Entity\Group'));
        $groupManager->expects($this->once())->method('findGroupBy')->will($this->returnValue($group));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('submit');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createGroupController($group, $groupManager, $formFactory)->putGroupAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeleteGroupAction()
    {
        $group = $this->createMock('FOS\UserBundle\Model\GroupInterface');

        $groupManager = $this->createMock('Sonata\UserBundle\Model\GroupManagerInterface');
        $groupManager->expects($this->once())->method('findGroupBy')->will($this->returnValue($group));
        $groupManager->expects($this->once())->method('deleteGroup')->will($this->returnValue($group));

        $view = $this->createGroupController($group, $groupManager)->deleteGroupAction(1);

        $this->assertEquals(['deleted' => true], $view);
    }

    public function testDeleteGroupInvalidAction()
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $groupManager = $this->createMock('Sonata\UserBundle\Model\GroupManagerInterface');
        $groupManager->expects($this->once())->method('findGroupBy')->will($this->returnValue(null));
        $groupManager->expects($this->never())->method('deleteGroup');

        $this->createGroupController(null, $groupManager)->deleteGroupAction(1);
    }

    /**
     * @param $group
     * @param $groupManager
     * @param $formFactory
     *
     * @return GroupController
     */
    public function createGroupController($group = null, $groupManager = null, $formFactory = null)
    {
        if (null === $groupManager) {
            $groupManager = $this->createMock('Sonata\UserBundle\Model\GroupManagerInterface');
        }
        if (null !== $group) {
            $groupManager->expects($this->once())->method('findGroupBy')->will($this->returnValue($group));
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        }

        return new GroupController($groupManager, $formFactory);
    }
}
