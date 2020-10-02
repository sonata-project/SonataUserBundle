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

namespace Sonata\UserBundle\Controller\Api\Nelmio_v4;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\UserBundle\Model\GroupInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\UserBundle\Controller\Api\BaseGroupController;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class GroupController extends BaseGroupController
{
    /**
     * Returns a paginated list of groups.
     *
     * @Operation(
     *     tags={"/api/user/groups"},
     *     summary="Returns a paginated list of groups.",
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for groups list pagination (1-indexed)",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of groups per page",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="orderBy",
     *         in="query",
     *         description="Query groups order by clause (key is field, value is direction)",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="enabled",
     *         in="query",
     *         description="Enables or disables the groups only?",
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
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for groups list pagination (1-indexed)")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of groups per page")
     * @QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Query groups order by clause (key is field, value is direction)")
     * @QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enables or disables the groups only?")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @return PagerInterface
     */
    public function getGroupsAction(ParamFetcherInterface $paramFetcher)
    {
        return parent::getGroupsAction($paramFetcher);
    }

    /**
     * Retrieves a specific group.
     *
     * @Operation(
     *     tags={"/api/user/groups"},
     *     summary="Retrieves a specific group.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="FOS\UserBundle\Model\GroupInterface"))
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when group is not found"
     *     )
     * )
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     *
     * @param string $id
     *
     * @return GroupInterface
     */
    public function getGroupAction($id)
    {
        return parent::getGroupAction($id);
    }

    /**
     * Adds a group.
     *
     * @Operation(
     *     tags={"/api/user/groups"},
     *     summary="Adds a group.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\UserBundle\Model\Group"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred during the group creation"
     *     )
     * )
     *
     * @param Request $request A Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return GroupInterface
     */
    public function postGroupAction(Request $request)
    {
        return parent::postGroupAction($request);
    }

    /**
     * Updates a group.
     *
     * @Operation(
     *     tags={"/api/user/groups"},
     *     summary="Updates a group.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @SWG\Schema(ref=@Model(type="Sonata\UserBundle\Model\Group"))
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred during the group creation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find group"
     *     )
     * )
     *
     * @param string  $id      Group identifier
     * @param Request $request A Symfony request
     *
     * @throws NotFoundHttpException
     *
     * @return GroupInterface
     */
    public function putGroupAction($id, Request $request)
    {
        return parent::putGroupAction($id, $request);
    }

    /**
     * Deletes a group.
     *
     * @Operation(
     *     tags={"/api/user/groups"},
     *     summary="Deletes a group.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when group is successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when an error has occurred during the group deletion"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when unable to find group"
     *     )
     * )
     *
     * @param string $id A Group identifier
     *
     * @throws NotFoundHttpException
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteGroupAction($id)
    {
        return parent::deleteGroupAction($id);
    }
}
