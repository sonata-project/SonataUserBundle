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

use Nelmio\ApiDocBundle\Annotation\Operation;
use Sonata\UserBundle\Tests\App\AppKernel;
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

        $matchingPath = $path;
        $matchingFormat = '';
        if (\strlen($matchingPath) >= 10 && false !== strpos($matchingPath, '.{_format}', -10)) {
            $matchingFormat = '.json';
            $matchingPath = str_replace('.{_format}', $matchingFormat, $path);
        }

        $matcher = $router->getMatcher();
        $requestContext = $router->getContext();

        foreach ($methods as $method) {
            $requestContext->setMethod($method);

            // Check paths like "/api/user/users.json".
            $match = $matcher->match($matchingPath);

            $this->assertSame($name, $match['_route']);

            if ($matchingFormat) {
                $this->assertSame(ltrim($matchingFormat, '.'), $match['_format']);
            }

            $matchingPathWithStrippedFormat = str_replace('.{_format}', '', $path);

            // Check paths like "/api/user/users".
            $match = $matcher->match($matchingPathWithStrippedFormat);

            $this->assertSame($name, $match['_route']);

            if ($matchingFormat) {
                $this->assertSame(ltrim($matchingFormat, '.'), $match['_format']);
            }
        }
    }

    public function getRoutes(): iterable
    {
        // API
        if (class_exists(Operation::class)) {
            yield ['app.swagger_ui', '/api/doc', ['GET']];
            yield ['app.swagger', '/api/doc.json', ['GET']];
        } else {
            yield ['nelmio_api_doc_index', '/api/doc/{view}', ['GET']];
        }

        // API - User
        yield ['sonata_api_user_user_get_users', '/api/user/users.{_format}', ['GET']];
        yield ['sonata_api_user_user_get_user', '/api/user/users/{id}.{_format}', ['GET']];
        yield ['sonata_api_user_user_post_user', '/api/user/users.{_format}', ['POST']];
        yield ['sonata_api_user_user_put_user', '/api/user/users/{id}.{_format}', ['PUT']];
        yield ['sonata_api_user_user_delete_user', '/api/user/users/{id}.{_format}', ['DELETE']];
        yield ['sonata_api_user_user_post_user_group', '/api/user/users/{userId}/groups/{groupId}.{_format}', ['POST']];
        yield ['sonata_api_user_user_delete_user_group', '/api/user/users/{userId}/groups/{groupId}.{_format}', ['DELETE']];

        // API - Group
        yield ['sonata_api_user_group_get_groups', '/api/user/groups.{_format}', ['GET']];
        yield ['sonata_api_user_group_get_group', '/api/user/groups/{id}.{_format}', ['GET']];
        yield ['sonata_api_user_group_post_group', '/api/user/groups.{_format}', ['POST']];
        yield ['sonata_api_user_group_put_group', '/api/user/groups/{id}.{_format}', ['PUT']];
        yield ['sonata_api_user_group_delete_group', '/api/user/groups/{id}.{_format}', ['DELETE']];
    }

    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }
}
