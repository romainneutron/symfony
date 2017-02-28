<?php

namespace Symfony\Component\Image\Tests\Issues;

use Symfony\Component\Image\Gd\Loader;
use Symfony\Component\Image\Exception\RuntimeException;

class Issue59Test extends \PHPUnit_Framework_TestCase
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

        $imagine
            ->open(__DIR__.'/../Fixtures/sample.gif')
            ->save($new)
        ;

        unlink($new);
    }
}
