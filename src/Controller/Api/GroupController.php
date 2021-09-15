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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\UserBundle\Form\Type\ApiGroupType;
use Sonata\UserBundle\Model\Group;
use Sonata\UserBundle\Model\GroupInterface;
use Sonata\UserBundle\Model\GroupManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class GroupController
{
    /**
     * @var GroupManagerInterface
     */
    protected $groupManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param GroupManagerInterface $groupManager Sonata group manager
     * @param FormFactoryInterface  $formFactory  Symfony form factory
     */
    public function __construct(GroupManagerInterface $groupManager, FormFactoryInterface $formFactory)
    {
        $this->groupManager = $groupManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Returns a paginated list of groups.
     *
     * @Operation(
     *     tags={"/api/user/groups"},
     *     summary="Returns a paginated list of groups.",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page for groups list pagination (1-indexed)",
     *         required=false,
     *         type="string"
     *     ),
     *     @OA\Parameter(
     *         name="count",
     *         in="query",
     *         description="Number of groups per page",
     *         required=false,
     *         type="string"
     *     ),
     *     @OA\Parameter(
     *         name="orderBy",
     *         in="query",
     *         description="Query groups order by clause (key is field, value is direction)",
     *         required=false,
     *         type="string"
     *     ),
     *     @OA\Parameter(
     *         name="enabled",
     *         in="query",
     *         description="Enables or disables the groups only?",
     *         required=false,
     *         type="string"
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @Model(type=PagerInterface::class)
     *     )
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for groups list pagination (1-indexed)")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of groups per page")
     * @QueryParam(name="orderBy", map=true, requirements="ASC|DESC", nullable=true, strict=true, description="Query groups order by clause (key is field, value is direction)")
     * @QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enables or disables the groups only?")
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     */
    public function getGroupsAction(ParamFetcherInterface $paramFetcher): PagerInterface
    {
        $supportedFilters = [
            'enabled' => '',
        ];

        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('count');
        $sort = $paramFetcher->get('orderBy');
        $criteria = array_intersect_key($paramFetcher->all(), $supportedFilters);

        $criteria = array_filter($criteria, static function ($value): bool {
            return null !== $value;
        });

        if (!$sort) {
            $sort = [];
        } elseif (!\is_array($sort)) {
            $sort = [$sort, 'asc'];
        }

        return $this->groupManager->getPager($criteria, $page, $limit, $sort);
    }

    /**
     * Retrieves a specific group.
     *
     * @Operation(
     *     tags={"/api/user/groups"},
     *     summary="Retrieves a specific group.",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @Model(type=GroupInterface::class)
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Returned when group is not found"
     *     )
     * )
     *
     * @View(serializerGroups={"sonata_api_read"}, serializerEnableMaxDepthChecks=true)
     */
    public function getGroupAction(int $id): GroupInterface
    {
        return $this->getGroup($id);
    }

    /**
     * Adds a group.
     *
     * @Operation(
     *     tags={"/api/user/groups"},
     *     summary="Adds a group.",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @Model(type=Group::class)
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returned when an error has occurred during the group creation"
     *     )
     * )
     *
     * @throws NotFoundHttpException
     */
    public function postGroupAction(Request $request): FOSRestView
    {
        return $this->handleWriteGroup($request);
    }

    /**
     * Updates a group.
     *
     * @Operation(
     *     tags={"/api/user/groups"},
     *     summary="Updates a group.",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @Model(type=Group::class)
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returned when an error has occurred during the group creation"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Returned when unable to find group"
     *     )
     * )
     *
     * @throws NotFoundHttpException
     */
    public function putGroupAction(int $id, Request $request): FOSRestView
    {
        return $this->handleWriteGroup($request, $id);
    }

    /**
     * Deletes a group.
     *
     * @Operation(
     *     tags={"/api/user/groups"},
     *     summary="Deletes a group.",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when group is successfully deleted"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returned when an error has occurred during the group deletion"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Returned when unable to find group"
     *     )
     * )
     *
     * @throws NotFoundHttpException
     */
    public function deleteGroupAction(int $id): FOSRestView
    {
        $group = $this->getGroup($id);

        $this->groupManager->deleteGroup($group);

        return FOSRestView::create(['deleted' => true]);
    }

    /**
     * Write a Group, this method is used by both POST and PUT action methods.
     */
    protected function handleWriteGroup(Request $request, ?int $id = null): FOSRestView
    {
        $groupClassName = $this->groupManager->getClass();
        $group = $id ? $this->getGroup($id) : new $groupClassName('');

        $form = $this->formFactory->createNamed('', ApiGroupType::class, $group, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return FOSRestView::create($form);
        }

        $group = $form->getData();
        $this->groupManager->updateGroup($group);

        $context = new Context();
        $context->setGroups(['sonata_api_read']);
        $context->enableMaxDepth();

        $view = FOSRestView::create($group);
        $view->setContext($context);

        return $view;
    }

    /**
     * Retrieves group with id $id or throws an exception if it doesn't exist.
     *
     * @throws NotFoundHttpException
     */
    protected function getGroup(int $id): GroupInterface
    {
        $group = $this->groupManager->findGroupBy(['id' => $id]);

        if (null === $group) {
            throw new NotFoundHttpException(sprintf('Group not found for identifier %s.', var_export($id, true)));
        }

        return $group;
    }
}
