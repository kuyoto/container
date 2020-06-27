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

/**
 * NullStreamTest.
 *
 * @category Library
 *
 * @author   Tolulope Kuyoro <nifskid1999@gmail.com>
 * @license  https://github.com/kuyoto/psr7-streams/blob/master/LICENSE.md (MIT License)
 */
class NullStreamTest extends TestCase
{
    /**
     * NullStreamTest::testProducesEquallyResultWithToStringAndRead()
     */
    public function testProducesEquallyResultWithToStringAndRead(): void
    {
        $stream = new NullStream();

        $streamRead = '';
        while (!$stream->eof()) {
            $streamRead .= $stream->read(8194);
        }

        $this->assertEquals($streamRead, (string)$stream);
    }

    /**
     * NullStreamTest::testIsAlwaysEof()
     */
    public function testIsAlwaysEof(): void
    {
        $stream = new NullStream();

        $this->assertTrue($stream->eof());
    }

    /**
     * NullStreamTest::testCorrectSize()
     */
    public function testCorrectSize(): void
    {
        $stream = new NullStream();

        $this->assertEquals(0, $stream->getSize());
    }

    /**
     * NullStreamTest::testReadsRemainingContents()
     */
    public function testReadsRemainingContents(): void
    {
        $stream = new NullStream();

        $stream->read(2);

        $this->assertEquals('', $stream->getContents());
    }

    /**
     * NullStreamTest::testIsRewindable()
     */
    public function testIsRewindable(): void
    {
        $stream = new NullStream();

        $stream->read(2);
        $stream->rewind();

        $this->assertEquals('', $stream->getContents());
    }

    /**
     * NullStreamTest::testCanSeek()
     */
    public function testCanSeek(): void
    {
        $stream = new NullStream();

        $stream->seek(3);

        $this->assertEquals(0, $stream->tell());
        $this->assertEquals('', $stream->getContents());
    }

    /**
     * NullStreamTest::testCannotBeWrittenTo()
     */
    public function testCannotBeWrittenTo(): void
    {
        $this->expectException(\RuntimeException::class);

        $stream = new NullStream();
        $this->assertFalse($stream->isWritable());

        $stream->write('x');
    }
}
