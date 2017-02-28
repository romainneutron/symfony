<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Image\Tests\Imagick;

use Symfony\Component\Image\Image\ImageInterface;
use Symfony\Component\Image\Image\Metadata\MetadataBag;
use Symfony\Component\Image\Image\Point;
use Symfony\Component\Image\Imagick\Loader;
use Symfony\Component\Image\Imagick\Image;
use Symfony\Component\Image\Image\Palette\CMYK;
use Symfony\Component\Image\Image\Palette\RGB;
use Symfony\Component\Image\Tests\Image\AbstractImageTest;
use Symfony\Component\Image\Image\Box;
use Symfony\Component\Image\Imagick\Image as ImagickImage;

class ImageTest extends AbstractImageTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick is not installed');
        }
    }

    protected function tearDown()
    {
        if (class_exists('Imagick')) {
            $prop = new \ReflectionProperty(ImagickImage::class, 'supportsColorspaceConversion');
            $prop->setAccessible(true);
            $prop->setValue(null);
        }

        parent::tearDown();
    }

    protected function getLoader()
    {
        return new Loader();
    }

    public function testImageResizeUsesProperMethodBasedOnInputAndOutputSizes()
    {
        $imagine = $this->getLoader();

        $image = $imagine->open(__DIR__.'/../Fixtures/resize/210-design-19933.jpg');

        $image
            ->resize(new Box(1500, 750))
            ->save(__DIR__.'/../Fixtures/resize/large.png')
        ;

        $image
            ->resize(new Box(100, 50))
            ->save(__DIR__.'/../Fixtures/resize/small.png')
        ;

        unlink(__DIR__.'/../Fixtures/resize/large.png');
        unlink(__DIR__.'/../Fixtures/resize/small.png');
    }

    public function testAnimatedGifResize()
    {
        $imagine = $this->getLoader();
        $image = $imagine->open(__DIR__.'/../Fixtures/anima3.gif');
        $image
            ->resize(new Box(150, 100))
            ->save(__DIR__.'/../Fixtures/resize/anima3-150x100-actual.gif', array('animated' => true))
        ;
        $this->assertImageEquals(
            $imagine->open(__DIR__.'/../Fixtures/resize/anima3-150x100.gif'),
            $imagine->open(__DIR__.'/../Fixtures/resize/anima3-150x100-actual.gif')
        );
        unlink(__DIR__.'/../Fixtures/resize/anima3-150x100-actual.gif');
    }

    // Older imagemagick versions does not support colorspace conversion
    public function testOlderImageMagickDoesNotAffectColorspaceUsageOnConstruct()
    {
        $palette = new CMYK();
        $imagick = $this->getMockBuilder('\Imagick')->getMock();
        $imagick->expects($this->any())
            ->method('setColorspace')
            ->will($this->throwException(new \RuntimeException('Method not supported')));

        $prop = new \ReflectionProperty(ImagickImage::class, 'supportsColorspaceConversion');
        $prop->setAccessible(true);
        $prop->setValue(false);

        return new Image($imagick, $palette, new MetadataBag());
    }

    /**
     * @depends testOlderImageMagickDoesNotAffectColorspaceUsageOnConstruct
     * @expectedException \Symfony\Component\Image\Exception\RuntimeException
     * @expectedExceptionMessage Your version of Imagick does not support colorspace conversions.
     */
    public function testOlderImageMagickDoesNotAffectColorspaceUsageOnPaletteChange($image)
    {
        $image->usePalette(new RGB());
    }

    public function testAnimatedGifCrop()
    {
        $imagine = $this->getLoader();
        $image = $imagine->open(__DIR__.'/../Fixtures/anima3.gif');
        $image
            ->crop(
                new Point(0, 0),
                new Box(150, 100)
            )
            ->save(__DIR__.'/../Fixtures/crop/anima3-topleft-actual.gif', array('animated' => true))
        ;
        $this->assertImageEquals(
            $imagine->open(__DIR__.'/../Fixtures/crop/anima3-topleft.gif'),
            $imagine->open(__DIR__.'/../Fixtures/crop/anima3-topleft-actual.gif')
        );
        unlink(__DIR__.'/../Fixtures/crop/anima3-topleft-actual.gif');
    }


    protected function supportMultipleLayers()
    {
        return true;
    }

    protected function getImageResolution(ImageInterface $image)
    {
        return $image->getImagick()->getImageResolution();
    }
}
