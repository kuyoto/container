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
 * String based PSR-7 stream.
 *
 * @category Library
 *
 * @author   Tolulope Kuyoro <nifskid1999@gmail.com>
 * @license  https://github.com/kuyoto/psr7-streams/blob/master/LICENSE.md (MIT License)
 */
class StringStream implements StreamInterface
{
    /**
     * @var string
     */
    private $string = '';

    /**
     * @var int
     */
    private $position = 0;

    /**
     * Constructor.
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->string;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return $this->position >= strlen($this->string);
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
    public function getContents()
    {
        return substr($this->string, $this->position);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return strlen($this->string);
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
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        $result = substr($this->string, $this->position, $length);
        $this->position += strlen($result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        switch ($whence) {
            case SEEK_CUR:
                $this->position += $offset;

                break;
            case SEEK_END:
                $this->position = strlen($this->string) + $offset;

                break;
            case SEEK_SET:
                $this->position = $offset;

                break;
        }
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
    public function write($string)
    {
        $this->string = substr_replace($this->string, $string, $this->position, strlen($string));
        $this->position += strlen($string);

        return strlen($string);
    }
}
