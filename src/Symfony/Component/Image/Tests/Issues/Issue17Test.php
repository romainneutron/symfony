<?php

namespace Symfony\Component\Image\Tests\Issues;

use Symfony\Component\Image\Image\ImageInterface;
use Symfony\Component\Image\Image\Box;
use Symfony\Component\Image\Gd\Loader;
use Symfony\Component\Image\Exception\RuntimeException;

class Issue17Test extends \PHPUnit_Framework_TestCase
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
        $size    = new Box(100, 10);
        $imagine = $this->getLoader();

        $imagine->open(__DIR__.'/../Fixtures/large.jpg')
            ->thumbnail($size, ImageInterface::THUMBNAIL_OUTBOUND)
            ->save(__DIR__.'/../Fixtures/resized.jpg');

        $this->assertTrue(file_exists(__DIR__.'/../Fixtures/resized.jpg'));
        $this->assertEquals(
            $size,
            $imagine->open(__DIR__.'/../Fixtures/resized.jpg')->getSize()
        );

        unlink(__DIR__.'/../Fixtures/resized.jpg');
    }
}
