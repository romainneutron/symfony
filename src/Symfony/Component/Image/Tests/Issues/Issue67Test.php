<?php

namespace Symfony\Component\Image\Tests\Issues;

use Symfony\Component\Image\Gd\Loader;
use Symfony\Component\Image\Exception\RuntimeException;

class Issue67Test extends \PHPUnit_Framework_TestCase
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

    /**
    * @expectedException Symfony\Component\Image\Exception\RuntimeException
    */
    public function testShouldThrowExceptionNotError()
    {
        $invalidPath = '/thispathdoesnotexist';

        $imagine = $this->getLoader();

        $imagine->open(__DIR__.'/../Fixtures/large.jpg')
            ->save($invalidPath . '/myfile.jpg');
    }
}
