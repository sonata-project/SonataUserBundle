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

namespace Sonata\UserBundle\Tests\Util;

use PHPUnit\Framework\TestCase;
use Sonata\UserBundle\Util\Canonicalizer;

class CanonicalizerTest extends TestCase
{
    /**
     * @dataProvider canonicalizeProvider
     *
     * @param $source
     * @param $expectedResult
     */
    public function testCanonicalize($source, $expectedResult)
    {
        $canonicalizer = new Canonicalizer();
        $this->assertSame($expectedResult, $canonicalizer->canonicalize($source));
    }

    /**
     * @return array
     */
    public function canonicalizeProvider()
    {
        return [
            [null, null],
            ['FOO', 'foo'],
            [\chr(171), \PHP_VERSION_ID < 50600 ? \chr(171) : '?'],
        ];
    }
}
