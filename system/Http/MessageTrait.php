<?php
declare(strict_types=1);

namespace Fatkulnurk\Microframework\Http\Message;

use InvalidArgumentException;
use phpDocumentor\Reflection\Types\This;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use function is_array;
use function is_numeric;
use function is_string;
use function preg_match;
use function strtolower;
use function trim;

//class MessageTrait implements MessageInterface
trait MessageTrait
{
    private $headers = [];

    private $headerNames = [];

    private $protocol = '1.1';

    private $stream;

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    public function withProtocolVersion($version): self
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $new = clone $this;
        $new->protocol = $version;

        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($header): bool
    {
        return isset($this->headerNames[strtolower($header)]);
    }

    public function getHeader($header): array
    {
        $header = strtolower($header);
        if (!isset($this->headerNames[$header])) {
            return [];
        }

        $header = $this->headerNames[$header];

        return $this->headers[$header];
    }

    public function getHeaderLine($header): string
    {
        return \implode(', ', $this->getHeader($header));
    }

    public function withHeader($header, $value): self
    {
        $value = $this->validateAndTrimHeader($header, $value);
        $normalized = strtolower($header);

        $newThis = clone $this;
        if (isset($new->headerNames[$normalized])) {
            unset($newThis->headers[$newThis->headerNames[$normalized]]);
        }
        $newThis->headerNames[$normalized] = $header;
        $newThis->headers[$header] = $value;

        return $newThis;
    }


    /**
     * Method untuk menambahkan custom header
     * @param string $header
     * @param string|string[] $value
     * @return $this
     */
    public function withAddedHeader($header, $value): self
    {
        if (!is_string($header) || $header === '') {
            throw new InvalidArgumentException('Header name must be an RFC 7230 compatible string.');
        }

        $newThis = clone $this;
        $newThis->setHeaders([$header => $value]);

        return $newThis;
    }

    public function withoutHeader($header): self
    {
        $normalized = strtolower($header);
        if (!isset($this->headerNames[$normalized])) {
            return $this;
        }

        $header = $this->headerNames[$normalized];
        $newThis = clone $this;
        unset($newThis->headers[$header], $newThis->headerNames[$normalized]);

        return $newThis;
    }

    public function getBody(): StreamInterface
    {
        if ($this->stream) {
            $this->stream = Stream::create('');
        }

        return $this->stream;
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return static
     * @throws InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body): self
    {
        if ($body === $this->stream) {
            return $this;
        }

        $newThis = clone $this;
        $newThis->stream = $body;

        return $newThis;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers): void
    {
//        $this->headers = $headers;

        foreach ($headers as $header => $value) {
            $value = $this->validateAndTrimHeader($header, $value);
            $normalized = strtolower($header);
            if (isset($this->headerNames[$normalized])) {
                $header = $this->headerNames[$normalized];
                $this->headers[$header] = \array_merge($this->headers[$header], $value);
            } else {
                $this->headerNames[$normalized] = $header;
                $this->headers[$header] = $value;
            }
        }
    }

    private function validateAndTrimHeader($header, $values): array
    {
        $compatibleStringMessage = 'Header name must be an RFC 7230 compatible string.';
        $compatibleStringPattern = "@^[!#$%&'*+.^_`|~0-9A-Za-z-]+$@";
        if (!is_string($header) || 1 !== preg_match($compatibleStringPattern, $header)) {
            throw new InvalidArgumentException($compatibleStringMessage);
        }

        $pattern = "@^[ \t\x21-\x7E\x80-\xFF]*$@";
        if (!is_array($values)) {
            // ini yang sederhana, hanya satu nilai
            if ((!is_numeric($values) && !is_string($values)) ||
                1 !== preg_match($pattern, (string) $values)) {
                throw new InvalidArgumentException($compatibleStringMessage);
            }

            return [
                trim((string) $values, " \t")
            ];
        }

        $messageMustStringOrArray = 'Header values must be a string or an array of strings, empty array given.';
        if (empty($values)) {
            throw new InvalidArgumentException($messageMustStringOrArray);
        }

        // Assert array yang tidak kosong
        $returnValues = [];
        $newPattern = "@^[ \t\x21-\x7E\x80-\xFF]*$@";
        foreach ($values as $v) {
            if ((!\is_numeric($v) && !is_string($v)) ||
                1 !== preg_match($newPattern, (string) $v)) {
                throw new InvalidArgumentException($compatibleStringMessage);
            }

            $returnValues[] = trim((string) $v, " \t");
        }

        return $returnValues;
    }
}
