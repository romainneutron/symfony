<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Image\Filter;

use Symfony\Component\Image\Exception\InvalidArgumentException;
use Symfony\Component\Image\Image\LoaderInterface;

/**
 * LoaderAware base class
 */
abstract class LoaderAware implements FilterInterface
{
    /**
     * An LoaderInterface instance.
     *
     * @var LoaderInterface
     */
    private $imagine;

    /**
     * Set LoaderInterface instance.
     *
     * @param LoaderInterface $imagine An LoaderInterface instance
     */
    public function setLoader(LoaderInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    /**
     * Get LoaderInterface instance.
     *
     * @return LoaderInterface
     *
     * @throws InvalidArgumentException
     */
    public function getLoader()
    {
        if (!$this->imagine instanceof LoaderInterface) {
            throw new InvalidArgumentException(sprintf('In order to use %s pass an Symfony\Component\Image\Image\LoaderInterface instance to filter constructor', get_class($this)));
        }

        return $this->imagine;
    }
}
