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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\UserBundle\Model\GroupManagerInterface;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Model\UserManagerInterface;
use Swagger\Annotations as SWG;
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

    public function __construct(UserManagerInterface $userManager, GroupManagerInterface $groupManager, FormFactoryInterface $formFactory)
    {
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Returns a paginated list of users.
     *
     * @Operation(
     *     tags={"/api/user/users"},
     *     summary="Returns a paginated list of users.",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for users list pagination (1-indexed)",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of users per page",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="orderBy",
     *         in="query",
     *         description="Query users order by clause (key is field, value is direction)",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="enabled",
     *         in="query",
     *         description="Enables or disables the users only?",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\DatagridBundle\Pager\PagerInterface"))
     *     )
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for users list pagination (1-indexed)")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of users per page")
     * @QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Query users order by clause (key is field, value is direction)")
     * @QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enables or disables the users only?")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
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

        $criteria = array_filter($criteria, static function ($value): bool {
            return null !== $value;
        });

        if (!$sort) {
            $sort = [];
        } elseif (!\is_array($sort)) {
            $sort = [$sort, 'asc'];
        }

        return $this->userManager->getPager($criteria, $page, $limit, $sort);
    }

    /**
     * Retrieves a specific user.
     *
     * @Operation(
     *     tags={"/api/user/users"},
     *     summary="Retrieves a specific user.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\UserBundle\Model\UserInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when user is not found"
     *     )
     * )
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id
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
     * @Operation(
     *     tags={"/api/user/users"},
     *     summary="Adds a user.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\UserBundle\Model\Group"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred during the user creation"
     *     )
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
     * @Operation(
     *     tags={"/api/user/users"},
     *     summary="Updates a user.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\UserBundle\Model\User"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred during the user creation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find user"
     *     )
     * )
     *
     * @param string  $id      User id
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
     * @Operation(
     *     tags={"/api/user/users"},
     *     summary="Deletes an user.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when user is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred during the user deletion"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find user"
     *     )
     * )
     *
     * @param string $id An User identifier
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
     * @Operation(
     *     tags={"/api/user/users"},
     *     summary="Attach a group to a user.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\UserBundle\Model\User"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred while user/group attachment"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find user or group"
     *     )
     * )
     *
     * @param string $userId  A User identifier
     * @param string $groupId A Group identifier
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
     * @Operation(
     *     tags={"/api/user/users"},
     *     summary="Detach a group to a user.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\UserBundle\Model\User"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error occurred while detaching the user from the group"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find user or group"
     *     )
     * )
     *
     * @param string $userId  A User identifier
     * @param string $groupId A Group identifier
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
     * @param string $id
     *
     * @throws NotFoundHttpException
     *
     * @return UserInterface
     */
    protected function getUser($id)
    {
        $user = $this->userManager->findUserBy(['id' => $id]);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('User not found for identifier %s.', var_export($id, true)));
        }

        return $user;
    }

    /**
     * Retrieves user with id $id or throws an exception if it doesn't exist.
     *
     * @param string $id
     *
     * @throws NotFoundHttpException
     *
     * @return GroupInterface
     */
    protected function getGroup($id)
    {
        $group = $this->groupManager->findGroupBy(['id' => $id]);

        if (null === $group) {
            throw new NotFoundHttpException(sprintf('Group not found for identifier %s.', var_export($id, true)));
        }

        return $group;
    }

    /**
     * Write an User, this method is used by both POST and PUT action methods.
     *
     * @param Request     $request Symfony request
     * @param string|null $id      An User identifier
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
