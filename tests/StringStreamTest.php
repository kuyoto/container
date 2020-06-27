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

use PHPUnit\Framework\TestCase;;

/**
 * StringStreamTest.
 *
 * @category Library
 *
 * @author   Tolulope Kuyoro <nifskid1999@gmail.com>
 * @license  https://github.com/kuyoto/psr7-streams/blob/master/LICENSE.md (MIT License)
 */
class StringStreamTest extends TestCase
{
    /**
     * StringStreamTest::testProducesEquallyResultWithToStringAndRead()
     */
    public function testProducesEquallyResultWithToStringAndRead(): void
    {
        $stream = new StringStream('test1');

        $streamRead = '';

        while (!$stream->eof()) {
            $streamRead .= $stream->read(8194);
        }

        $this->assertEquals($streamRead, (string)$stream);
    }

    /**
     * StringStreamTest::testCorrectSize()
     */
    public function testCorrectSize(): void
    {
        $stream = new StringStream('test1');

        $this->assertEquals(5, $stream->getSize());
    }

    /**
     * StringStreamTest::testReadsRemainingContents()
     */
    public function testReadsRemainingContents(): void
    {
        $stream = new StringStream('test1');

        $stream->read(2);

        $this->assertEquals('st1', $stream->getContents());
    }

    /**
     * StringStreamTest::testIsRewindable()
     */
    public function testIsRewindable(): void
    {
        $stream = new StringStream('test1');

        $stream->read(2);
        $stream->rewind();

        $this->assertEquals('test1', $stream->getContents());
    }

    /**
     * StringStreamTest::testCanSeek()
     */
    public function testCanSeek(): void
    {
        $stream = new StringStream('test1');

        $stream->seek(3);

        $this->assertEquals(3, $stream->tell());
        $this->assertEquals('t1', $stream->getContents());
    }

    /**
     * StringStreamTest::testCannotBeWrittenTo()
     */
    public function testCannotBeWrittenTo(): void
    {
        $stream = new StringStream('test1');

        $this->assertTrue($stream->isWritable());

        $stream->write('x');

        $this->assertEquals('est1', $stream->getContents());
        $this->assertEquals('xest1', (string) $stream);
    }
}
