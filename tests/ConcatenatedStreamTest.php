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
 * ConcatenatedStreamTest.
 *
 * @category Library
 *
 * @author   Tolulope Kuyoro <nifskid1999@gmail.com>
 * @license  https://github.com/kuyoto/psr7-streams/blob/master/LICENSE.md (MIT License)
 */
class ConcatenatedStreamTest extends TestCase
{
    /**
     * ConcatenatedStreamTest::testStreamsAreReadable()
     */
    public function testStreamsAreReadable(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $stream = $this->prophesize(StreamInterface::class);
        $stream->seek(Argument::type('integer'), SEEK_CUR)->willReturn();
        $stream->isReadable()->willReturn(false);

        new ConcatenatedStream([$stream->reveal()]);
    }

    /**
     * ConcatenatedStreamTest::testSupportsOnlySeekCur()
     */
    public function testSupportsOnlySeekCur(): void
    {
        $this->expectException(\RuntimeException::class);

        $stream = $this->prophesize(StreamInterface::class);
        $stream->isReadable()->willReturn(true);

        $conc = new ConcatenatedStream([$stream->reveal()]);
        $conc->seek(100, SEEK_CUR);
    }

    /**
     * ConcatenatedStreamTest::testClosesEachStream()
     */
    public function testClosesEachStream(): void
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->isReadable()->willReturn(true);
        $stream->isSeekable()->willReturn(true);
        $stream->close()->willReturn();

        $conc = new ConcatenatedStream([$stream->reveal()]);
        $conc->close();

        $this->assertSame('', (string) $conc);
    }

    /**
     * ConcatenatedStreamTest::testCanDetach()
     */
    public function testCanDetach(): void
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->isReadable()->willReturn(true);
        $stream->isSeekable()->willReturn(true);
        $stream->detach()->willReturn(null);

        $conc = new ConcatenatedStream([$stream->reveal()]);

        $this->assertNull($conc->detach());
    }

    /**
     * ConcatenatedStreamTest::testCanDetermineSizeFromMultipleStreams()
     */
    public function testCanDetermineSizeFromMultipleStreams(): void
    {
        $stream1 = $this->prophesize(StreamInterface::class);
        $stream1->isReadable()->willReturn(true);
        $stream1->isSeekable()->willReturn(true);
        $stream1->getSize()->willReturn(2);

        $stream2 = $this->prophesize(StreamInterface::class);
        $stream2->isReadable()->willReturn(true);
        $stream2->isSeekable()->willReturn(true);
        $stream2->getSize()->willReturn(3);

        $conc = new ConcatenatedStream([$stream1->reveal(), $stream2->reveal()]);

        $this->assertEquals(5, $conc->getSize());
    }

    /**
     * ConcatenatedStreamTest::testCanReadFromMultipleStreams()
     */
    public function testCanReadFromMultipleStreams(): void
    {
        $stream1 = $this->prophesize(StreamInterface::class);
        $stream1->isReadable()->willReturn(true);
        $stream1->isSeekable()->willReturn(true);
        $stream1->eof()->willReturn(false);
        $stream1->rewind()->willReturn();
        $stream1->getContents()->willReturn('foo');

        $stream2 = $this->prophesize(StreamInterface::class);
        $stream2->isReadable()->willReturn(true);
        $stream2->isSeekable()->willReturn(true);
        $stream2->eof()->willReturn(true);
        $stream2->rewind()->willReturn();
        $stream2->getContents()->willReturn('bar');

        $conc = new ConcatenatedStream([$stream1->reveal(), $stream2->reveal()]);

        $this->assertFalse($conc->eof());
        $this->assertEquals(0, $conc->tell());
        $this->assertEquals('foobar', (string) $conc);
        $this->assertTrue($conc->eof());
    }

    /**
     * ConcatenatedStreamTest::testReturnsEmptyMetadata()
     */
    public function testReturnsEmptyMetadata(): void
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->isReadable()->willReturn(true);
        $stream->isSeekable()->willReturn(true);
        $stream->getMetadata(Argument::type('string'))->willReturn(null);

        $conc = new ConcatenatedStream([$stream->reveal()]);

        $this->assertEquals([], $conc->getMetadata());
        $this->assertNull($conc->getMetadata('foo'));
    }

    /**
     * ConcatenatedStreamTest::testDoesNotNeedStreams()
     */
    public function testDoesNotNeedStreams(): void
    {
        $stream = new ConcatenatedStream([]);

        $this->assertEquals('', (string) $stream);
    }

    /**
     * ConcatenatedStreamTest::testCatchesExceptionsWhenCastingToString()
     */
    public function testCatchesExceptionsWhenCastingToString(): void
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->isReadable()->willReturn(true);
        $stream->isSeekable()->willReturn(true);
        $stream->eof()->willReturn(false);
        $stream->getContents()->willThrow(new \RuntimeException());

        $conc = new ConcatenatedStream([$stream->reveal()]);

        $this->assertFalse($conc->eof());
        $this->assertSame('', (string) $conc);
    }
}
