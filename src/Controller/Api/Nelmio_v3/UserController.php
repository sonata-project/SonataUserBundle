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

namespace Sonata\UserBundle\Controller\Api\Nelmio_v3;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\UserBundle\Controller\Api\BaseUserController;
use Sonata\UserBundle\Model\UserInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class UserController extends BaseUserController
{
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
        return parent::getUsersAction($paramFetcher);
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
        return parent::getUserAction($id);
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
        return parent::postUserAction($request);
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
        return parent::putUserAction($id, $request);
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
        return parent::deleteUserAction($id);
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
        return parent::postUserGroupAction($userId, $groupId);
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
        return parent::deleteUserGroupAction($userId, $groupId);
    }
}
