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

namespace Sonata\UserBundle\Controller\Api;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View as FOSRestView;
use FOS\UserBundle\Model\GroupInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\UserBundle\Model\GroupManagerInterface;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class UserController
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var GroupManagerInterface
     */
    protected $groupManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param UserManagerInterface  $userManager
     * @param GroupManagerInterface $groupManager
     * @param FormFactoryInterface  $formFactory
     */
    public function __construct(UserManagerInterface $userManager, GroupManagerInterface $groupManager, FormFactoryInterface $formFactory)
    {
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Returns a paginated list of users.
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"={"sonata_api_read"}}
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for users list pagination (1-indexed)")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of users by page")
     * @QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Query users order by clause (key is field, value is direction")
     * @QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/disabled users only?")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return PagerInterface
     */
    public function getUsersAction(ParamFetcherInterface $paramFetcher)
    {
        $supporedCriteria = [
            'enabled' => '',
        ];

        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('count');
        $sort = $paramFetcher->get('orderBy');
        $criteria = array_intersect_key($paramFetcher->all(), $supporedCriteria);

        foreach ($criteria as $key => $value) {
            if (null === $value) {
                unset($criteria[$key]);
            }
        }

        if (!$sort) {
            $sort = [];
        } elseif (!is_array($sort)) {
            $sort = [$sort, 'asc'];
        }

        return $this->userManager->getPager($criteria, $page, $limit, $sort);
    }

    /**
     * Retrieves a specific user.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="user id"}
     *  },
     *  output={"class"="Sonata\UserBundle\Model\UserInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when user is not found"
     *  }
     * )
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return UserInterface
     */
    public function getUserAction($id)
    {
        return $this->getUser($id);
    }

    /**
     * Adds an user.
     *
     * @ApiDoc(
     *  input={"class"="sonata_user_api_form_user", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\UserBundle\Model\User", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while user creation",
     *  }
     * )
     *
     * @param Request $request A Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return UserInterface
     */
    public function postUserAction(Request $request)
    {
        return $this->handleWriteUser($request);
    }

    /**
     * Updates an user.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="user identifier"}
     *  },
     *  input={"class"="sonata_user_api_form_user", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\UserBundle\Model\User", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while user creation",
     *      404="Returned when unable to find user"
     *  }
     * )
     *
     * @param int     $id      User id
     * @param Request $request A Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return UserInterface
     */
    public function putUserAction($id, Request $request)
    {
        return $this->handleWriteUser($request, $id);
    }

    /**
     * Deletes an user.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="user identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when user is successfully deleted",
     *      400="Returned when an error has occurred while user deletion",
     *      404="Returned when unable to find user"
     *  }
     * )
     *
     * @param int $id An User identifier
     *
     * @throws NotFoundHttpException
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteUserAction($id)
    {
        $user = $this->getUser($id);

        $this->userManager->deleteUser($user);

        return ['deleted' => true];
    }

    /**
     * Attach a group to a user.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="userId", "dataType"="integer", "requirement"="\d+", "description"="user identifier"},
     *      {"name"="groupId", "dataType"="integer", "requirement"="\d+", "description"="group identifier"}
     *  },
     *  output={"class"="Sonata\UserBundle\Model\User", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while user/group attachment",
     *      404="Returned when unable to find user or group"
     *  }
     * )
     *
     * @param int $userId  A User identifier
     * @param int $groupId A Group identifier
     *
     * @throws NotFoundHttpException
     * @throws \RuntimeException
     *
     * @return UserInterface
     */
    public function postUserGroupAction($userId, $groupId)
    {
        $user = $this->getUser($userId);
        $group = $this->getGroup($groupId);

        if ($user->hasGroup($group)) {
            return FOSRestView::create([
                'error' => sprintf('User "%s" already has group "%s"', $userId, $groupId),
            ], 400);
        }

        $user->addGroup($group);
        $this->userManager->updateUser($user);

        return ['added' => true];
    }

    /**
     * Detach a group to a user.
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="userId", "dataType"="integer", "requirement"="\d+", "description"="user identifier"},
     *      {"name"="groupId", "dataType"="integer", "requirement"="\d+", "description"="group identifier"}
     *  },
     *  output={"class"="Sonata\UserBundle\Model\User", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while user/group detachment",
     *      404="Returned when unable to find user or group"
     *  }
     * )
     *
     * @param int $userId  A User identifier
     * @param int $groupId A Group identifier
     *
     * @throws NotFoundHttpException
     * @throws \RuntimeException
     *
     * @return UserInterface
     */
    public function deleteUserGroupAction($userId, $groupId)
    {
        $user = $this->getUser($userId);
        $group = $this->getGroup($groupId);

        if (!$user->hasGroup($group)) {
            return FOSRestView::create([
                'error' => sprintf('User "%s" has not group "%s"', $userId, $groupId),
            ], 400);
        }

        $user->removeGroup($group);
        $this->userManager->updateUser($user);

        return ['removed' => true];
    }

    /**
     * Retrieves user with id $id or throws an exception if it doesn't exist.
     *
     * @param $id
     *
     * @throws NotFoundHttpException
     *
     * @return UserInterface
     */
    protected function getUser($id)
    {
        $user = $this->userManager->findUserBy(['id' => $id]);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('User (%d) not found', $id));
        }

        return $user;
    }

    /**
     * Retrieves user with id $id or throws an exception if it doesn't exist.
     *
     * @param $id
     *
     * @throws NotFoundHttpException
     *
     * @return GroupInterface
     */
    protected function getGroup($id)
    {
        $group = $this->groupManager->findGroupBy(['id' => $id]);

        if (null === $group) {
            throw new NotFoundHttpException(sprintf('Group (%d) not found', $id));
        }

        return $group;
    }

    /**
     * Write an User, this method is used by both POST and PUT action methods.
     *
     * @param Request  $request Symfony request
     * @param int|null $id      An User identifier
     *
     * @return FormInterface
     */
    protected function handleWriteUser($request, $id = null)
    {
        $user = $id ? $this->getUser($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_user_api_form_user', $user, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $form->getData();
            $this->userManager->updateUser($user);

            $context = new Context();
            $context->setGroups(['sonata_api_read']);
            $context->enableMaxDepth();

            $view = FOSRestView::create($user);
            $view->setContext($context);

            return $view;
        }

        return $form;
    }
}
