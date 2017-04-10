<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sonata\UserBundle\DependencyInjection\Configuration;
use Sonata\UserBundle\Tests\Helpers\PHPUnit_Framework_TestCase;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    public function getConfiguration()
    {
        return new Configuration();
    }

    public function testDefault()
    {
        $this->assertProcessedConfigurationEquals(array(
            array(),
        ), array(
            'security_acl' => false,
            'table' => array(
                'user_group' => 'fos_user_user_group',
            ),
            'google_authenticator' => array(
                'enabled' => false,
            ),
            'manager_type' => 'orm',
            'admin' => array(
                'user' => array(
                    'controller' => 'SonataAdminBundle:CRUD',
                    'translation' => 'SonataUserBundle',
                ),
                'group' => array(
                    'controller' => 'SonataAdminBundle:CRUD',
                    'translation' => 'SonataUserBundle',
                ),
            ),
            'profile' => array(
                'default_avatar' => 'bundles/sonatauser/default_avatar.png',
            ),
        ));
    }
}
