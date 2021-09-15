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

namespace Sonata\UserBundle\Controller\Api\Legacy;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View as FOSRestView;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\UserBundle\Form\Type\ApiGroupType;
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
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="int", "description"="Group identifier"}
     *  },
     *  output={"class"="Sonata\UserBundle\Model\GroupInterface", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when group is not found"
     *  }
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
     * @ApiDoc(
     *  input={"class"="sonata_user_api_form_group", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\UserBundle\Model\Group", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred during the group creation",
     *  }
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
     * @throws NotFoundHttpException
     */
    public function putGroupAction(int $id, Request $request): FOSRestView
    {
        return $this->handleWriteGroup($request, $id);
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
