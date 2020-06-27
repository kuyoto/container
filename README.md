# PSR-7 Streams

This repository provides several [PSR-7](http://www.php-fig.org/psr/psr-7/) stream decorators.

## Installation

The recommnended way to install this library is through [composer](https://getcomposer.org):

```bash
composer require kuyoto/psr7-streams
```

## Stream Decorators

This library consists of the following `Psr\Http\Message\StreamInterface` impementations:

### ConcatenatedStream

`Kuyoto\Psr7\Stream\ConcatenatedStream`

Reads from multiple streams, one after the other.

```php
use Kuyoto\Psr7\Stream\StringStream;
use Kuyoto\Psr7\Stream\ConcatenatedStream;

$a = new StringStream('foo');
$b = new StringStream('bar');

$composed = new ConcatenatedStream([$a, $b]);

echo $composed; // foobar.
```

### NoSeekStream

`Kuyoto\Psr7\Stream\NoSeekStream`

It wraps a stream and does not allow seeking.

```php
use Kuyoto\Psr7\Stream\StringStream;
use Kuyoto\Psr7\Stream\NoSeekStream;

$stream = new StringStream('foo');
$noSeek = new NoSeekStream($stream);

echo $noSeek->read(3); // foo

var_export($noSeek->isSeekable()); // false

$noSeek->seek(0);

var_export($noSeek->read(3)); // NULL
```

### NullStream

`Kuyoto\Psr7\Stream\NullStream`

NullStream does not store any data written to it.

```php
use Kuyoto\Psr7\Stream\NullStream;

$stream = new NullStream();

echo $stream->read(3); // ''
var_export($stream->getSize()); // NULL
```

### StringStream

`Kuyoto\Psr7\Stream\StringStream`

StringStream allows instantiation of a stream from a the provided string.

```php
use Kuyoto\Psr7\Stream\StringStream;

$stream = new StringStream('foo');

echo $stream->read(2); // fo

var_export($stream->getSize()); // 3
```

## Implementing Stream Decorators

Creating a stream decorator is very easy thanks to the `Kuyoto\Psr7\Stream\StreamDecorator`. This decorator provides methods that implement `Psr\Http\Message\StreamInterface` by proxying to an underlying stream. Just `use` the `StreamDecoratorTrait` and implement your custom methods.

## Testing

```bash
composer test
```

## License

The package is an open-sourced software licensed under the [MIT License](LICENSE).
