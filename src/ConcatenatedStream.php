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
 * Reads from multiple streams, one after the other.
 *
 * This is a read-only stream decorator.
 *
 * @category Library
 *
 * @author   Tolulope Kuyoro <nifskid1999@gmail.com>
 * @license  https://github.com/kuyoto/psr7-streams/blob/master/LICENSE.md (MIT License)
 */
class ConcatenatedStream implements StreamInterface
{
    /**
     * @var StreamInterface[] a list of streams
     */
    private $streams = [];

    /**
     * @var bool
     */
    private $seekable = true;

    /**
     * @var int
     */
    private $current = 0;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * Constructor.
     *
     * @param iterable|StreamInterface[] $stream a list of streams
     */
    public function __construct(iterable $streams)
    {
        foreach ($streams as &$stream) {
            $this->addStream($stream);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        try {
            $this->rewind();

            return $this->getContents();
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->seekable = true;

        foreach ($this->streams as $stream) {
            $stream->close();
        }

        $this->streams = [];
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        $this->seekable = true;

        foreach ($this->streams as $stream) {
            $stream->detach();
        }

        $this->streams = [];

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        if (!isset($this->streams[$this->current])) {
            return true;
        }

        if (!$this->streams[$this->current]->eof()) {
            return false;
        }

        return isset($this->streams[$this->current + 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        $result = '';

        while (isset($this->streams[$this->current])) {
            $result .= $this->streams[$this->current]->getContents();

            ++$this->current;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        return $key ? null : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        $size = 0;

        foreach ($this->streams as $stream) {
            $streamSize = $stream->getSize();

            if ($streamSize === null) {
                return null;
            }

            $size += $streamSize;
        }

        return $size;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        return $this->seekable;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        $result = '';

        while (strlen($result) < $length && isset($this->streams[$this->current])) {
            $result .= $this->streams[$this->current]->read($length);

            if ($this->streams[$this->current]->eof()) {
                ++$this->current;
            }
        }

        $this->position += strlen($result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
        $this->current = 0;

        foreach ($this->streams as $stream) {
            $stream->rewind();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $seek = 0;

        while (isset($this->streams[$this->current]) && $offset > $seek) {
            $streamSize = $this->streams[$this->current]->getSize();

            if ($this->streams[$this->current]->seek($offset - $seek) === -1) {
                return -1;
            }

            if ($streamSize > $offset - $seek) {
                $seek += $offset - $seek;
            } else {
                $seek += $streamSize;
            }

            if ($this->streams[$this->current]->eof()) {
                ++$this->current;
            }
        }

        $this->position = $seek;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        throw new \RuntimeException('Cannot write to stream');
    }

    /**
     * Adds a stream.
     *
     * @param StreamInterface $stream the stream to append. Must be readable.
     *
     * @throws \InvalidArgumentException if the stream is not readable
     */
    private function addStream(StreamInterface $stream): void
    {
        if (!$stream->isReadable()) {
            throw new \InvalidArgumentException('Each stream must be readable');
        }

        if (!$stream->isSeekable()) {
            $this->seekable = false;
        }

        $this->streams[] = $stream;
    }
}
