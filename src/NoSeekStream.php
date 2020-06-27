<?php

/**
 * PSR-7 Stream (https://github.com/kuyoto/psr7-streams).
 *
 * PHP version 7
 *
 * @category  Library
 *
 * @author    Tolulope Kuyoro <nifskid1999@gmail.com>
 * @copyright 2020 Tolulope Kuyoro <nifskid1999@gmail.com>
 * @license   https://github.com/kuyoto/psr7-streams/blob/master/LICENSE.md (MIT License)
 *
 * @version   GIT: master
 */

declare(strict_types=1);

namespace Kuyoto\Psr7\Stream;

use Psr\Http\Message\StreamInterface;

/**
 * Stream decorator that prevents a stream from being seeked.
 *
 * @category Library
 *
 * @author   Tolulope Kuyoro <nifskid1999@gmail.com>
 * @license  https://github.com/kuyoto/psr7-streams/blob/master/LICENSE.md (MIT License)
 */
class NoSeekStream implements StreamInterface
{
    use StreamDecorator;

    /**
     * Constructor.
     */
    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        throw new \RuntimeException(sprintf('Cannot seek "%s"', __CLASS__));
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        throw new \RuntimeException(sprintf('Cannot seek "%s"', __CLASS__));
    }
}
