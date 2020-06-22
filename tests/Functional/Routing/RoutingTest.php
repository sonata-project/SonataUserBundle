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

namespace Sonata\UserBundle\Tests\Functional\Routing;

use Sonata\UserBundle\Tests\Functional\App\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class RoutingTest extends WebTestCase
{
    /**
     * @group legacy
     *
     * @dataProvider getRoutes
     */
    public function testRoutes(string $name, string $path, array $methods): void
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');

        $route = $router->getRouteCollection()->get($name);

        $this->assertNotNull($route);
        $this->assertSame($path, $route->getPath());
        $this->assertEmpty(array_diff($methods, $route->getMethods()));
    }

    public function getRoutes(): iterable
    {
        yield ['nelmio_api_doc_index', '/api/doc/{view}', ['GET']];
        yield ['sonata_api_user_user_sonata_user_api_user_getusers', '/api/user/users', ['GET']];
        yield ['sonata_api_user_user_sonata_user_api_user_getuser', '/api/user/user/(id}', ['GET']];
        yield ['sonata_api_user_user_sonata_user_api_user_postuser', '/api/user/user', ['POST']];
        yield ['sonata_api_user_user_sonata_user_api_user_putuser', '/api/user/user/(id}', ['PUT']];
        yield ['sonata_api_user_user_sonata_user_api_user_deleteuser', '/api/user/user/(id}', ['DELETE']];
        yield ['sonata_api_user_user_sonata_user_api_user_postusergroup', '/api/user/user/(userId}/{groupId}', ['POST']];
        yield ['sonata_api_user_user_sonata_user_api_user_deleteusergroup', '/api/user/user/(userId}/{groupId}', ['DELETE']];
        yield ['sonata_api_user_group_sonata_user_api_group_getgroups', '/api/user/groups', ['GET']];
        yield ['sonata_api_user_group_sonata_user_api_group_getgroup', '/api/user/group/{id}', ['GET']];
        yield ['sonata_api_user_group_sonata_user_api_group_postgroup', '/api/user/group', ['POST']];
        yield ['sonata_api_user_group_sonata_user_api_group_putgroup', '/api/user/group/{id}', ['PUT']];
        yield ['sonata_api_user_group_sonata_user_api_group_deletegroup', '/api/user/group/{id}', ['DELETE']];
    }

    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }
}
