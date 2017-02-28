<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Image\Tests\Image;

use Symfony\Component\Image\Image\Box;
use Symfony\Component\Image\Image\Point;
use Symfony\Component\Image\Image\Palette\RGB;
use Symfony\Component\Image\Exception\InvalidArgumentException;
use Symfony\Component\Image\Exception\OutOfBoundsException;
use Symfony\Component\Image\Image\LoaderInterface;

abstract class AbstractLayersTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        $palette = new RGB();
        $image = $this->getLoader()->create(new Box(20, 20), $palette->color('#FFFFFF'));
        foreach ($image->layers() as $layer) {
            $layer
                ->draw()
                ->polygon(array(new Point(0, 0),new Point(0, 20),new Point(20, 20),new Point(20, 0)), $palette->color('#FF0000'), true);
        }
        $image->layers()->merge();

        $this->assertEquals('#ff0000', (string) $image->getColorAt(new Point(5,5)));
    }

    public function testLayerArrayAccess()
    {
        $firstImage = $this->getImage(__DIR__ . "/../Fixtures/pink.gif");
        $secondImage = $this->getImage(__DIR__ . "/../Fixtures/yellow.gif");
        $thirdImage = $this->getImage(__DIR__ . "/../Fixtures/blue.gif");

        $layers = $firstImage->layers();

        $this->assertCount(1, $layers);

        $layers[] = $secondImage;

        $this->assertCount(2, $layers);
        $this->assertLayersEquals($firstImage, $layers[0]);
        $this->assertLayersEquals($secondImage, $layers[1]);

        $layers[1] = $thirdImage;

        $this->assertCount(2, $layers);
        $this->assertLayersEquals($firstImage, $layers[0]);
        $this->assertLayersEquals($thirdImage, $layers[1]);

        $layers[] = $secondImage;

        $this->assertCount(3, $layers);
        $this->assertLayersEquals($firstImage, $layers[0]);
        $this->assertLayersEquals($thirdImage, $layers[1]);
        $this->assertLayersEquals($secondImage, $layers[2]);

        $this->assertTrue(isset($layers[2]));
        $this->assertTrue(isset($layers[1]));
        $this->assertTrue(isset($layers[0]));

        unset($layers[1]);

        $this->assertCount(2, $layers);
        $this->assertLayersEquals($firstImage, $layers[0]);
        $this->assertLayersEquals($secondImage, $layers[1]);

        $this->assertFalse(isset($layers[2]));
        $this->assertTrue(isset($layers[1]));
        $this->assertTrue(isset($layers[0]));
    }

    public function testLayerAddGetSetRemove()
    {
        $firstImage = $this->getImage(__DIR__ . "/../Fixtures/pink.gif");
        $secondImage = $this->getImage(__DIR__ . "/../Fixtures/yellow.gif");
        $thirdImage = $this->getImage(__DIR__ . "/../Fixtures/blue.gif");

        $layers = $firstImage->layers();

        $this->assertCount(1, $layers);

        $layers->add($secondImage);

        $this->assertCount(2, $layers);
        $this->assertLayersEquals($firstImage, $layers->get(0));
        $this->assertLayersEquals($secondImage, $layers->get(1));

        $layers->set(1, $thirdImage);

        $this->assertCount(2, $layers);
        $this->assertLayersEquals($firstImage, $layers->get(0));
        $this->assertLayersEquals($thirdImage, $layers->get(1));

        $layers->add($secondImage);

        $this->assertCount(3, $layers);
        $this->assertLayersEquals($firstImage, $layers->get(0));
        $this->assertLayersEquals($thirdImage, $layers->get(1));
        $this->assertLayersEquals($secondImage, $layers->get(2));

        $this->assertTrue($layers->has(2));
        $this->assertTrue($layers->has(1));
        $this->assertTrue($layers->has(0));

        $layers->remove(1);

        $this->assertCount(2, $layers);
        $this->assertLayersEquals($firstImage, $layers->get(0));
        $this->assertLayersEquals($secondImage, $layers->get(1));

        $this->assertFalse($layers->has(2));
        $this->assertTrue($layers->has(1));
        $this->assertTrue($layers->has(0));
    }

    /**
     * @dataProvider provideInvalidArguments
     */
    public function testLayerArrayAccessInvalidArgumentExceptions($offset)
    {
        $firstImage = $this->getImage(__DIR__ . "/../Fixtures/pink.gif");
        $secondImage = $this->getImage(__DIR__ . "/../Fixtures/pink.gif");

        $layers = $firstImage->layers();

        try {
            $layers[$offset] = $secondImage;
            $this->fail('An exception should have been raised');
        } catch (InvalidArgumentException $e) {

        }
    }

    /**
     * @dataProvider provideOutOfBoundsArguments
     */
    public function testLayerArrayAccessOutOfBoundsExceptions($offset)
    {
        $firstImage = $this->getImage(__DIR__ . "/../Fixtures/pink.gif");
        $secondImage = $this->getImage(__DIR__ . "/../Fixtures/pink.gif");

        $layers = $firstImage->layers();

        try {
            $layers[$offset] = $secondImage;
            $this->fail('An exception should have been raised');
        } catch (OutOfBoundsException $e) {

        }
    }

    public function testAnimateEmpty()
    {
        $image = $this->getImage();
        $layers = $image->layers();

        $layers[] = $this->getImage(__DIR__ . "/../Fixtures/pink.gif");
        $layers[] = $this->getImage(__DIR__ . "/../Fixtures/yellow.gif");
        $layers[] = $this->getImage(__DIR__ . "/../Fixtures/blue.gif");

        $target = __DIR__ . '/../Fixtures/temporary-gif.gif';

        $image->save($target, array(
            'animated' => true,
        ));

        @unlink($target);
    }

    /**
     * @dataProvider provideAnimationParameters
     */
    public function testAnimateWithParameters($delay, $loops)
    {
        $image = $this->getImage(__DIR__ . "/../Fixtures/pink.gif");
        $layers = $image->layers();

        $layers[] = $this->getImage(__DIR__ . "/../Fixtures/yellow.gif");
        $layers[] = $this->getImage(__DIR__ . "/../Fixtures/blue.gif");

        $target = __DIR__ . '/../Fixtures/temporary-gif.gif';

        $image->save($target, array(
            'animated' => true,
            'animated.delay' => $delay,
            'animated.loops' => $loops,
        ));

        @unlink($target);
    }

    public function provideAnimationParameters()
    {
        return array(
            array(0, 0),
            array(500, 0),
            array(0, 10),
            array(5000, 10),
        );
    }

    /**
     * @expectedException Symfony\Component\Image\Exception\InvalidArgumentException
     * @dataProvider provideWrongAnimationParameters
     */
    public function testAnimateWithWrongParameters($delay, $loops)
    {
        $image = $this->getImage(__DIR__ . "/../Fixtures/pink.gif");
        $layers = $image->layers();

        $layers[] = $this->getImage(__DIR__ . "/../Fixtures/yellow.gif");
        $layers[] = $this->getImage(__DIR__ . "/../Fixtures/blue.gif");

        $target = __DIR__ . '/../Fixtures/temporary-gif.gif';

        $image->save($target, array(
            'animated' => true,
            'animated.delay' => $delay,
            'animated.loops' => $loops,
        ));

        @unlink($target);
    }

    public function provideWrongAnimationParameters()
    {
        return array(
            array(-1, 0),
            array(500, -1),
            array(-1, 10),
            array(0, -1),
        );
    }

    public function provideInvalidArguments()
    {
        return array(
            array('lambda'),
            array('0'),
            array('1'),
            array(1.0),
        );
    }

    public function provideOutOfBoundsArguments()
    {
        return array(
            array(-1),
            array(2),
        );
    }

    abstract protected function getImage($path = null);

    /**
     * @return LoaderInterface
     */
    abstract protected function getLoader();
    abstract protected function assertLayersEquals($expected, $actual);
}
