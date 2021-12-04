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

namespace Sonata\UserBundle\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Sonata\Doctrine\Model\ManagerInterface;

/**
 * NEXT_MAJOR: Remove this class.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 *
 * @deprecated since sonata-project/user-bundle 4.x, to be removed in 5.0.
 */
final class UserSerializerHandler implements SubscribingHandlerInterface
{
    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @var string[]
     */
    private static $formats = ['json', 'xml', 'yml'];

    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public static function getSubscribingMethods(): array
    {
        $type = 'sonata_user_user_id';
        $methods = [];

        foreach (static::$formats as $format) {
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => $format,
                'type' => $type,
                'method' => 'serializeObjectToId',
            ];

            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => $format,
                'type' => $type,
                'method' => 'deserializeObjectFromId',
            ];
        }

        return $methods;
    }

    /**
     * Serialize data object to id.
     *
     * @param object $data
     *
     * @return int|null
     */
    public function serializeObjectToId(SerializationVisitorInterface $visitor, $data, array $type, Context $context)
    {
        $className = $this->manager->getClass();

        if ($data instanceof $className) {
            return $visitor->visitInteger($data->getId(), $type);
        }
    }

    /**
     * Deserialize object from its id.
     *
     * @param int $data
     *
     * @return object|null
     */
    public function deserializeObjectFromId(SerializationVisitorInterface $visitor, $data, array $type)
    {
        return $this->manager->findOneBy(['id' => $data]);
    }
}
