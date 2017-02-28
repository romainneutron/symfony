<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Image\Tests\Functional;

use Symfony\Component\Image\Image\Point;
use Symfony\Component\Image\Gd\Loader;
use Symfony\Component\Image\Exception\RuntimeException;

class GdTransparentGifHandlingTest extends \PHPUnit_Framework_TestCase
{
    private function getLoader()
    {
        try {
            $imagine = new Loader();
        } catch (RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        return $imagine;
    }

    public function testShouldResize()
    {
        $imagine = $this->getLoader();
        $new     = sys_get_temp_dir()."/sample.jpeg";

        $image = $imagine->open(__DIR__.'/../Fixtures/xparent.gif');
        $size  = $image->getSize()->scale(0.5);

        $image
            ->resize($size)
        ;

        $imagine
            ->create($size)
            ->paste($image, new Point(0, 0))
            ->save($new)
        ;

        unlink($new);
    }
}
