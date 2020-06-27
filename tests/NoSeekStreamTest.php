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

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\StreamInterface;

/**
 * NoSeekStreamTest.
 *
 * @category Library
 *
 * @author   Tolulope Kuyoro <nifskid1999@gmail.com>
 * @license  https://github.com/kuyoto/psr7-streams/blob/master/LICENSE.md (MIT License)
 */
class NoSeekStreamTest extends TestCase
{
    /**
     * NoSeekStreamTest::testCannotSeek()
     */
    public function testCannotSeek(): void
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->seek(Argument::any(), Argument::any())->willReturn();
        $stream->isSeekable()->willReturn(false);

        $stream = new NoSeekStream($stream->reveal());

        $this->assertFalse($stream->isSeekable());
        $this->expectException(\RuntimeException::class);

        $stream->seek(2);
    }
}
