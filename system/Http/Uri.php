<?php

declare(strict_types=1);

namespace Fatkulnurk\Microframework\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use function is_string;
use function parse_url;
use function preg_replace_callback;
use function strtolower;
use function trim;

/*
 * Class Uri
 * Kegunaan untuk mengolah uri
 *
 * Dokumentasi Tutorial
 * - https://www.php.net/manual/en/function.parse-url.php
 * */
final class Uri implements UriInterface
{
    /**
     * @var array
     */
    private const SCHEMES = [
        'http' => 80,
        'https' => 443
    ];

    /**
     * @var string
     */
    private const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';

    /**
     * @var string
     */
    private const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /**
     * Untuk menyimpan scheme
     * @var string $scheme
     */
    private $scheme = '';

    /** @var string $userInfo Uri user info. */
    private $userInfo = '';

    /** @var string $host Uri host. */
    private $host = '';

    /** @var int|null $port Uri port. */
    private $port;

    /**  @var string $path Uri dari path. */
    private $path = '';

    /**  @var string $query Uri query string. */
    private $query = '';

    /** @var string $fragment Uri fragment. */
    private $fragment = '';

    public function __construct(string $uri = '')
    {
        if ($uri !== '') {
            /*
             * parse_url Menghasilkan array dengan key
             * scheme - e.g. http
             * host, port, user, pass, path, query, fragment
            */
            $parse_url = parse_url($uri);

            if ($parse_url === false) {
                throw new \InvalidArgumentException("Unable to parse URI: $uri");
            }

            $this->scheme   = isset($parse_url['scheme']) ? strtolower($parse_url['scheme']) : '';
            $this->host     = isset($parse_url['host']) ? strtolower($parse_url['host']) : '';
            $this->port     = isset($parse_url['port']) ? $this->filterPort($parse_url['port']) : null;
            $this->userInfo = $parse_url['user'] ?? '';
            $this->path     = isset($parse_url['path']) ? $this->filterPath($parse_url['path']) : '';
            $this->query    = isset($parse_url['query']) ? $this->filterQueryAndFragment($parse_url['query']) : '';
            $this->fragment = isset($parse_url['fragment']) ?
                $this->filterQueryAndFragment($parse_url['fragment']) : '';
            if (isset($parse_url['pass'])) {
                $this->userInfo .= ':' . $parse_url['pass'];
            }
        }
    }


    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Ambil informasi URI authority
     * @see    https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string URI authority dalam format "[user-info@]host[:port]".
     */
    public function getAuthority()
    {
        if ($this->host === '') {
            return '';
        }

        $uriAuthority = $this->host;
        if ($this->userInfo !== '') {
            $uriAuthority = $this->userInfo . '@' . $uriAuthority;
        }

        if ($this->port !== null) {
            $uriAuthority = $uriAuthority . ':' . $this->port;
        }

        return $uriAuthority;
    }

    /**
     * @return string
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @param string $scheme
     * @return $this
     */
    public function withScheme($scheme): self
    {
        if (!is_string($scheme)) {
            throw new InvalidArgumentException('Scheme must be a string');
        }

        $scheme = strtolower($scheme);
        if ($this->scheme === $scheme) {
            return $this;
        }

        $newUri = clone $this;
        $newUri->scheme = $scheme;
        $newUri->port = $newUri->filterPort($newUri->port);

        return $newUri;
    }

    /**
     * @param string $user
     * @param null $password
     * @return $this
     */
    public function withUserInfo($user, $password = null): self
    {
        if ($password !== null && $password !== '') {
            $user .= ':' . $password;
        }

        if ($this->userInfo === $user) {
            return $this;
        }

        $newUri = clone $this;
        $newUri->userInfo = $user;

        return $newUri;
    }

    /**
     * @param string $host
     * @return $this|Uri|UriInterface
     */
    public function withHost($host): self
    {
        if (!\is_string($host)) {
            throw new InvalidArgumentException("Host must be a string");
        }

        $host = strtolower($host);
        if ($this->host === $host) {
            return $this;
        }

        $newUri = clone $this;
        $newUri->host = $host;

        return $newUri;
    }

    /**
     * @param int|null $port
     * @return $this
     */
    public function withPort($port): self
    {
        $port = $this->filterPort($port);
        if ($this->port === $port) {
            return $this;
        }

        $newUri = clone $this;
        $newUri->port = $port;

        return $newUri;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function withPath($path): self
    {
        $path = $this->filterPath($path);
        if ($this->path === $path) {
            return $this;
        }

        $newUri = clone $this;
        $newUri->path = $path;

        return $newUri;
    }

    /**
     * @param string $query
     * @return $this
     */
    public function withQuery($query): self
    {
        $query = $this->filterQueryAndFragment($query);
        if ($this->query === $query) {
            return $this;
        }

        $newUri = clone $this;
        $newUri->query = $query;

        return $newUri;
    }

    /**
     * @param string $fragment
     * @return $this|Uri|UriInterface
     */
    public function withFragment($fragment)
    {
        $fragment = $this->filterQueryAndFragment($fragment);
        if ($this->fragment === $fragment) {
            return $this;
        }

        $newUri = clone $this;
        $newUri->fragment = $fragment;

        return $newUri;
    }

    /**
     * Dipanggil jika ada casting object
     * @return string
     */
    public function __toString(): string
    {
        return self::createUriString(
            $this->scheme,
            $this->getAuthority(),
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * Buat string URI dari berbagai bagian uri,
     * tujuannya agar membentuk sebuah uri.
     *
     * @param string $scheme
     * @param string $authority
     * @param string $path
     * @param string $query
     * @param string $fragment
     * @return string
     */
    private static function createUriString(
        string $scheme,
        string $authority,
        string $path,
        string $query,
        string $fragment
    ): string {
        $uri = '';
        if ($scheme !== '') {
            $uri .= $scheme . ':';
        }

        if ($authority !== '') {
            $uri .= '//' . $authority;
        }

        if ($path !== '') {
            if ($path[0] !== '') {
                if ($authority !== '') {
                    // jika path tidak menentu & authority ditemukan
                    // maka path harus diawali tanda slash "/"
                    $path = '/' . $path;
                }
            } elseif (isset($path[1]) && $path[1] === '/') {
                if ($authority =='') {
                    $path = '/' . trim($path, '/');
                }
            }

            $uri .= $path;
        }

        if ($query !== '') {
            $uri .= '?' . $query;
        }

        if ($fragment !== '') {
            $uri .= '#' . $fragment;
        }

        return $uri;
    }


    /*
     * Tidak termasuk bagian yang mesti di implementasikan
     * Hanya tambahan untuk keperluan bantuan
     * */

    /**
     * @param string $scheme
     * @param int $port
     * @return bool
     */
    private static function isNonStandardPort(string $scheme, int $port): bool
    {
        return !isset(self::SCHEMES[$scheme]) || $port !== self::SCHEMES[$scheme];
    }

    /**
     * untuk mendapatkan port yang standard
     * @param $port
     * @return int|null
     */
    private function filterPort($port): ?int
    {
        if ($port === null) {
            return null;
        }

        $port = (int) $port;
        // cek apakah port lebih dari 0 atau port lebih dari 65535
        if (0 > $port || 0xffff < $port) {
            throw new InvalidArgumentException(
                \sprintf('Invalid port: %d . Port must be between 0 and 65535', $port)
            );
        }

        return self::isNonStandardPort($this->scheme, $port) ? $port : null;
    }

    /**
     * Method untuk melakukan Encode URI
     * Fungsi rawurlencode () adalah fungsi inbuilt di PHP
     * yang digunakan untuk menyandikan URL (Uniform Resource Locator)
     * menurut RFC (Uniform Resource Identifier) 3986.
     *
     * Insert
     * <a href="http://example.com/department_list_script/',rawurlencode('sales and marketing/Miami'), '">
     * Output
     * <a href="http://example.com/department_list_script/sales%20and%20marketing%2FMiami">
     *
     * @see https://www.php.net/manual/en/function.rawurlencode.php
     * @see https://www.geeksforgeeks.org/php-rawurlencode-function/
     * @param array $match
     * @return string
     */
    private static function rawurlencodeMatchZero(array $match): string
    {
        return rawurlencode($match[0]);
    }

    /**
     * Method untuk melakukan filter terhadap path
     * Untuk memastikan bahwa path adalah valid
     *
     * preg_replace_callback
     * pencarian ekspresi reguler dan ganti menggunakan callback
     *
     * Parameters
     * - pattern
     *   The pattern to search for. It can be either a string or an array with strings.
     * - callback
     *   A callback that will be called and passed an array of matched elements in the subject string.
     *   The callback should return the replacement string.
     *   This is the callback signature:
     *
     * @see https://www.php.net/manual/en/function.preg-replace-callback.php
     * @param $path
     * @return string
     */
    private function filterPath($path): string
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException("Path must be a string");
        }

        return preg_replace_callback(
            '/(?:[^' .self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            [__CLASS__, 'rawurlencodeMatchZero'],
            $path
        );
    }

    /**
     * Untuk melakukan pengecekan terhadap query dan fragment
     * @param $str
     * @return string
     */
    private function filterQueryAndFragment($str): string
    {
        if (!is_string($str)) {
            throw new InvalidArgumentException('Query and Fragment must be a string');
        }

        return preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
            [__CLASS__, 'rawurlencodeMatchZero'],
            $str
        );
    }
}
