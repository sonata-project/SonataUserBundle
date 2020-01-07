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

namespace Sonata\UserBundle\Tests\Block;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Block\ProfileMenuBlockService;
use Sonata\UserBundle\Menu\ProfileMenuBuilder;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Wojciech Błoszyk <wbloszyk@gmail.com>
 */
class ProfileMenuBlockServiceTest extends TestCase
{
    /**
     * @var ProfileMenuBlockService
     */
    private $profileMenuBlockService;

    protected function setUp(): void
    {
        /**
         * prepere profileMenuBuilder.
         */
        $menu = $this->createMock(ItemInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $factory = $this->createMock(FactoryInterface::class);
        $factory
            ->method('createItem')
            ->willReturn($menu);

        $profileMenuBuilder = new ProfileMenuBuilder($factory, $translator, [], $eventDispatcher);

        /**
         * prepere profileMenuBlockService.
         */
        $engineInterfaceMock = $this->createMock(EngineInterface::class);
        $menuProviderInterfaceMock = $this->createMock(MenuProviderInterface::class);

        $this->profileMenuBlockService = new ProfileMenuBlockService(
            'sonata.user.block.profile_menu',
            $engineInterfaceMock,
            $menuProviderInterfaceMock,
            $profileMenuBuilder
        );
    }

    public function testGetName(): void
    {
        $this->assertSame('User Profile Menu', $this->profileMenuBlockService->getName());
    }
}
