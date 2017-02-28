<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Image\Filter\Advanced;

use Symfony\Component\Image\Filter\FilterInterface;
use Symfony\Component\Image\Image\ImageInterface;
use Symfony\Component\Image\Image\BoxInterface;
use Symfony\Component\Image\Image\Point;
use Symfony\Component\Image\Image\PointInterface;
use Symfony\Component\Image\Image\Palette\Color\ColorInterface;
use Symfony\Component\Image\Image\LoaderInterface;

/**
 * A canvas filter
 */
class Canvas implements FilterInterface
{
    /**
     * @var BoxInterface
     */
    private $size;

    /**
     * @var PointInterface
     */
    private $placement;

    /**
     * @var ColorInterface
     */
    private $background;

    /**
     * @var LoaderInterface
     */
    private $imagine;

    /**
     * Constructs Canvas filter with given width and height and the placement of the current image
     * inside the new canvas
     *
     * @param LoaderInterface $imagine
     * @param BoxInterface     $size
     * @param PointInterface   $placement
     * @param ColorInterface   $background
     */
    public function __construct(LoaderInterface $imagine, BoxInterface $size, PointInterface $placement = null, ColorInterface $background = null)
    {
        $this->imagine = $imagine;
        $this->size = $size;
        $this->placement = $placement ?: new Point(0, 0);
        $this->background = $background;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        $canvas = $this->imagine->create($this->size, $this->background);
        $canvas->paste($image, $this->placement);

        return $canvas;
    }
}
