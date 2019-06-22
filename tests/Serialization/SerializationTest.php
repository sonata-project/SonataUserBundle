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

namespace Sonata\UserBundle\Tests\Serialization;

use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Tests\Entity\User;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
class SerializationTest extends TestCase
{
    /**
     * @dataProvider getUserData
     */
    public function testUserSerialization(array $expected, ?string $serializationGroup, array $userData): void
    {
        $user = new User();

        $user->setUsername($userData['username']);
        $user->setEnabled($userData['enabled']);
        $user->setRoles($userData['roles']);
        $user->setDateOfBirth($userData['date_of_birth']);
        $user->setFirstname($userData['firstname']);
        $user->setLastname($userData['lastname']);
        $user->setWebsite($userData['website']);
        $user->setBiography($userData['biography']);
        $user->setGender($userData['gender']);
        $user->setLocale($userData['locale']);
        $user->setTimezone($userData['timezone']);
        $user->setPhone($userData['phone']);
        $user->setFacebookUid($userData['facebook_uid']);
        $user->setFacebookName($userData['facebook_name']);
        $user->setFacebookData($userData['facebook_data']);
        $user->setTwitterUid($userData['twitter_uid']);
        $user->setTwitterName($userData['twitter_name']);
        $user->setTwitterData($userData['twitter_data']);
        $user->setGplusUid($userData['gplus_uid']);
        $user->setGplusName($userData['gplus_name']);
        $user->setGplusData($userData['gplus_data']);
        $user->setToken($userData['token']);
        $user->setTwoStepVerificationCode($userData['two_step_verification_code']);
        $user->setCreatedAt($userData['created_at']);
        $user->setUpdatedAt($userData['updated_at']);

        $serializerBuilder = SerializerBuilder::create();
        $serializerBuilder->addMetadataDir(__DIR__.'/../../src/Resources/config/serializer', 'Sonata\\UserBundle');
        $serializerBuilder->configureHandlers(static function (HandlerRegistryInterface $handlerRegistry): void {
            $handlerRegistry->registerSubscribingHandler(new DateHandler(\DateTime::ATOM, 'UTC', false));
        });

        $serializer = $serializerBuilder->build();

        $context = SerializationContext::create();
        if (null !== $serializationGroup) {
            $context->setGroups([$serializationGroup]);
        }

        $jsonDecoded = json_decode($serializer->serialize($user, 'json', $context), true);

        ksort($expected);
        ksort($jsonDecoded);

        $this->assertSame($expected, $jsonDecoded);
    }

    public function getUserData(): iterable
    {
        return [
            'with_group_sonata_api_read' => [
                'expected' => [
                    'date_of_birth' => '1986-03-22T18:45:00-03:00',
                    'firstname' => 'Incog',
                    'lastname' => 'Nito',
                    'website' => 'https://en.wikipedia.org/wiki/Incognito',
                    'biography' => 'Once upon a time ...',
                    'gender' => User::GENDER_UNKNOWN,
                    'locale' => 'es_AR',
                    'timezone' => 'America/Argentina/Buenos_Aires',
                    'phone' => '0054 9 11 12345678',
                    'facebook_uid' => '0123456789a',
                    'facebook_name' => 'LifeInvader',
                    'facebook_data' => ['motto' => 'I\'m a teapot'],
                    'twitter_uid' => '0123456789b',
                    'twitter_name' => 'Birdy',
                    'twitter_data' => ['ancestor' => 'Dinosaur'],
                    'gplus_uid' => '0123456789c',
                    'gplus_name' => 'EmptyResultSet',
                    'gplus_data' => ['users_count' => '-'],
                    'token' => 'some_secure_token',
                    'two_step_verification_code' => 'baby/steps',
                    'created_at' => '2012-11-10T00:00:00+00:00',
                    'updated_at' => '2019-06-21T20:19:18+00:00',
                ],
                'serialization_group' => 'sonata_api_read',
                'user_data' => [
                    'username' => 'johndoe',
                    'enabled' => true,
                    'roles' => ['ROLE_LAZY'],
                    'date_of_birth' => new \DateTime('1986-03-22 18:45:00 GMT-3'),
                    'firstname' => 'Incog',
                    'lastname' => 'Nito',
                    'website' => 'https://en.wikipedia.org/wiki/Incognito',
                    'biography' => 'Once upon a time ...',
                    'gender' => User::GENDER_UNKNOWN,
                    'locale' => 'es_AR',
                    'timezone' => 'America/Argentina/Buenos_Aires',
                    'phone' => '0054 9 11 12345678',
                    'facebook_uid' => '0123456789a',
                    'facebook_name' => 'LifeInvader',
                    'facebook_data' => ['motto' => 'I\'m a teapot'],
                    'twitter_uid' => '0123456789b',
                    'twitter_name' => 'Birdy',
                    'twitter_data' => ['ancestor' => 'Dinosaur'],
                    'gplus_uid' => '0123456789c',
                    'gplus_name' => 'EmptyResultSet',
                    'gplus_data' => ['users_count' => '-'],
                    'token' => 'some_secure_token',
                    'two_step_verification_code' => 'baby/steps',
                    'created_at' => new \DateTime('2012-11-10 00:00:00 UTC'),
                    'updated_at' => new \DateTime('2019-06-21 20:19:18 UTC'),
                ],
            ],
            'with_group_sonata_api_write' => [
                'expected' => [
                    'date_of_birth' => '1986-03-22T18:45:00-03:00',
                    'firstname' => 'Incog',
                    'lastname' => 'Nito',
                    'website' => 'https://en.wikipedia.org/wiki/Incognito',
                    'biography' => 'Once upon a time ...',
                    'gender' => User::GENDER_UNKNOWN,
                    'locale' => 'es_AR',
                    'timezone' => 'America/Argentina/Buenos_Aires',
                    'phone' => '0054 9 11 12345678',
                    'facebook_uid' => '0123456789a',
                    'facebook_name' => 'LifeInvader',
                    'facebook_data' => ['motto' => 'I\'m a teapot'],
                    'twitter_uid' => '0123456789b',
                    'twitter_name' => 'Birdy',
                    'twitter_data' => ['ancestor' => 'Dinosaur'],
                    'gplus_uid' => '0123456789c',
                    'gplus_name' => 'EmptyResultSet',
                    'gplus_data' => ['users_count' => '-'],
                    'token' => 'some_secure_token',
                    'two_step_verification_code' => 'baby/steps',
                ],
                'serialization_group' => 'sonata_api_write',
                'user_data' => [
                    'username' => 'johndoe',
                    'enabled' => true,
                    'roles' => ['ROLE_LAZY'],
                    'date_of_birth' => new \DateTime('1986-03-22 18:45:00 GMT-3'),
                    'firstname' => 'Incog',
                    'lastname' => 'Nito',
                    'website' => 'https://en.wikipedia.org/wiki/Incognito',
                    'biography' => 'Once upon a time ...',
                    'gender' => User::GENDER_UNKNOWN,
                    'locale' => 'es_AR',
                    'timezone' => 'America/Argentina/Buenos_Aires',
                    'phone' => '0054 9 11 12345678',
                    'facebook_uid' => '0123456789a',
                    'facebook_name' => 'LifeInvader',
                    'facebook_data' => ['motto' => 'I\'m a teapot'],
                    'twitter_uid' => '0123456789b',
                    'twitter_name' => 'Birdy',
                    'twitter_data' => ['ancestor' => 'Dinosaur'],
                    'gplus_uid' => '0123456789c',
                    'gplus_name' => 'EmptyResultSet',
                    'gplus_data' => ['users_count' => '-'],
                    'token' => 'some_secure_token',
                    'two_step_verification_code' => 'baby/steps',
                    'created_at' => new \DateTime('2012-11-10 00:00:00 UTC'),
                    'updated_at' => new \DateTime('2019-06-21 20:19:18 UTC'),
                ],
            ],
            'without_group' => [
                'expected' => [
                    'username' => 'johndoe',
                    'enabled' => true,
                    'roles' => ['ROLE_LAZY'],
                    'date_of_birth' => '1986-03-22T18:45:00-03:00',
                    'firstname' => 'Incog',
                    'lastname' => 'Nito',
                    'website' => 'https://en.wikipedia.org/wiki/Incognito',
                    'biography' => 'Once upon a time ...',
                    'gender' => User::GENDER_UNKNOWN,
                    'locale' => 'es_AR',
                    'timezone' => 'America/Argentina/Buenos_Aires',
                    'phone' => '0054 9 11 12345678',
                    'facebook_uid' => '0123456789a',
                    'facebook_name' => 'LifeInvader',
                    'facebook_data' => ['motto' => 'I\'m a teapot'],
                    'twitter_uid' => '0123456789b',
                    'twitter_name' => 'Birdy',
                    'twitter_data' => ['ancestor' => 'Dinosaur'],
                    'gplus_uid' => '0123456789c',
                    'gplus_name' => 'EmptyResultSet',
                    'gplus_data' => ['users_count' => '-'],
                    'token' => 'some_secure_token',
                    'two_step_verification_code' => 'baby/steps',
                    'created_at' => '2012-11-10T00:00:00+00:00',
                    'updated_at' => '2019-06-21T20:19:18+00:00',
                ],
                'serialization_group' => null,
                'user_data' => [
                    'username' => 'johndoe',
                    'enabled' => true,
                    'roles' => ['ROLE_LAZY'],
                    'date_of_birth' => new \DateTime('1986-03-22 18:45:00 GMT-3'),
                    'firstname' => 'Incog',
                    'lastname' => 'Nito',
                    'website' => 'https://en.wikipedia.org/wiki/Incognito',
                    'biography' => 'Once upon a time ...',
                    'gender' => User::GENDER_UNKNOWN,
                    'locale' => 'es_AR',
                    'timezone' => 'America/Argentina/Buenos_Aires',
                    'phone' => '0054 9 11 12345678',
                    'facebook_uid' => '0123456789a',
                    'facebook_name' => 'LifeInvader',
                    'facebook_data' => ['motto' => 'I\'m a teapot'],
                    'twitter_uid' => '0123456789b',
                    'twitter_name' => 'Birdy',
                    'twitter_data' => ['ancestor' => 'Dinosaur'],
                    'gplus_uid' => '0123456789c',
                    'gplus_name' => 'EmptyResultSet',
                    'gplus_data' => ['users_count' => '-'],
                    'token' => 'some_secure_token',
                    'two_step_verification_code' => 'baby/steps',
                    'created_at' => new \DateTime('2012-11-10 00:00:00 UTC'),
                    'updated_at' => new \DateTime('2019-06-21 20:19:18 UTC'),
                ],
            ],
        ];
    }
}
