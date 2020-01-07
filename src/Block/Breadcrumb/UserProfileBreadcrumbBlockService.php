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

namespace Sonata\UserBundle\Block\Breadcrumb;

use Knp\Menu\ItemInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;

/**
 * Class for user breadcrumbs.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
final class UserProfileBreadcrumbBlockService extends BaseUserProfileBreadcrumbBlockService
{
    public function getName(): string
    {
        return 'sonata.user.block.breadcrumb_profile';
    }

    protected function getMenu(BlockContextInterface $blockContext): ItemInterface
    {
        $menu = $this->getRootMenu($blockContext);
        $menu->addChild('sonata_user_profile_breadcrumb_edit', [
            'route' => 'sonata_user_profile_edit',
            'extras' => ['translation_domain' => 'SonataUserBundle'],
        ]);

        return $menu;
    }
}
