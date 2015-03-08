<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class FooTest extends \PHPUnit_Framework_TestCase
{
    public function testFooGetBar()
    {
        $foo = new \Foo\Foo('bar', 'baz');

        $this->assertEquals(
            'bar',
            $foo->getBar()
        );
    }
}
