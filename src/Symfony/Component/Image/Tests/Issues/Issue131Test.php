<?php

namespace Symfony\Component\Image\Tests\Issues;

use Symfony\Component\Image\Imagick\Loader as ImagickLoader;
use Symfony\Component\Image\Gmagick\Loader as GmagickLoader;
use Symfony\Component\Image\Exception\RuntimeException;

class Issue131Test extends \PHPUnit_Framework_TestCase
{

    private function getTemporaryDir()
    {
        $tempDir = tempnam(sys_get_temp_dir(), 'imagine');

        unlink($tempDir);
        mkdir($tempDir);

        return $tempDir;
    }

    private function getDirContent($dir)
    {
        $filenames = array();

        foreach (new \DirectoryIterator($dir) as $fileinfo) {
            if ($fileinfo->isFile()) {
                $filenames[] = $fileinfo->getPathname();
            }
        }

        return $filenames;
    }

    private function getImagickLoader($file)
    {
        try {
            $imagine = new ImagickLoader();
            $image = $imagine->open($file);
        } catch (RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        return $image;
    }

    private function getGmagickLoader($file)
    {
        try {
            $imagine = new GmagickLoader();
            $image = $imagine->open($file);
        } catch (RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        return $image;
    }

    public function testShouldSaveOneFileWithImagick()
    {
        $dir = realpath($this->getTemporaryDir());
        $targetFile = $dir . '/myfile.png';

        $imagine = $this->getImagickLoader(__DIR__ . '/multi-layer.psd');

        $imagine->save($targetFile);

        if ( ! $this->probeOneFileAndCleanup($dir, $targetFile)) {
            $this->fail('Imagick failed to generate one file');
        }
    }

    public function testShouldSaveOneFileWithGmagick()
    {
        $dir = realpath($this->getTemporaryDir());
        $targetFile = $dir . '/myfile.png';

        $imagine = $this->getGmagickLoader(__DIR__ . '/multi-layer.psd');

        $imagine->save($targetFile);

        if ( ! $this->probeOneFileAndCleanup($dir, $targetFile)) {
            $this->fail('Gmagick failed to generate one file');
        }
    }

    private function probeOneFileAndCleanup($dir, $targetFile)
    {
        $retval = true;
        $files = $this->getDirContent($dir);
        $retval = $retval && count($files) === 1;
        $file = current($files);
        $retval = $retval && $targetFile === $file;

        foreach ($files as $file) {
            unlink($file);
        }

        rmdir($dir);

        return $retval;
    }
}
