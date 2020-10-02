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

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\UserBundle\Model\GroupInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sonata\DatagridBundle\Pager\PagerInterface;
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
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"={"sonata_api_read"}}
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Group identifier"}
     *  },
     *  output={"class"="FOS\UserBundle\Model\GroupInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when group is not found"
     *  }
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
     * @ApiDoc(
     *  input={"class"="sonata_user_api_form_group", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\UserBundle\Model\Group", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred during the group creation",
     *  }
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Group identifier"}
     *  },
     *  input={"class"="sonata_user_api_form_group", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\UserBundle\Model\Group", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred during the group creation",
     *      404="Returned when unable to find group"
     *  }
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Group identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when group is successfully deleted",
     *      400="Returned when an error has occurred during the group deletion",
     *      404="Returned when unable to find group"
     *  }
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
