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
        yield ['sonata_api_user_user_get_users', '/api/user/users', ['GET']];
        yield ['sonata_api_user_user_get_user', '/api/user/user/(id}', ['GET']];
        yield ['sonata_api_user_user_post_user', '/api/user/user', ['POST']];
        yield ['sonata_api_user_user_put_user', '/api/user/user/(id}', ['PUT']];
        yield ['sonata_api_user_user_delete_user', '/api/user/user/(id}', ['DELETE']];
        yield ['sonata_api_user_user_post_user_group', '/api/user/user/(userId}/{groupId}', ['POST']];
        yield ['sonata_api_user_user_delete_user_group', '/api/user/user/(userId}/{groupId}', ['DELETE']];
        yield ['sonata_api_user_group_get_groups', '/api/user/groups', ['GET']];
        yield ['sonata_api_user_group_get_group', '/api/user/group/{id}', ['GET']];
        yield ['sonata_api_user_group_post_group', '/api/user/group', ['POST']];
        yield ['sonata_api_user_group_put_group', '/api/user/group/{id}', ['PUT']];
        yield ['sonata_api_user_group_delete_group', '/api/user/group/{id}', ['DELETE']];
    }

    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }
}
