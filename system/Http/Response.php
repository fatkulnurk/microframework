<?php
declare(strict_types=1);

namespace Fatkulnurk\Microframework\Http\Message;

use Fatkulnurk\Microframework\Core\Singleton;
use Fatkulnurk\Microframework\Http\Data\Document\Document;
use Fatkulnurk\Microframework\Http\Data\Document\DocumentFactory;
use Fatkulnurk\Microframework\Http\Data\Document\JsonFactory;
use Fatkulnurk\Microframework\Http\Data\Document\XmlFactory;
use Fatkulnurk\Microframework\Http\Data\View\Page;
use Fatkulnurk\Microframework\Http\Data\View\TemplateFactory;
use Fatkulnurk\Microframework\Http\Data\View\TwigTemplateFactory;
use Psr\Http\Message\{ResponseInterface, StreamInterface};
use function is_int;
use function is_string;

final class Response implements ResponseInterface
{
//    use MessageTrait;

    use \Nyholm\Psr7\MessageTrait;
    use Singleton;

    /** @var array Map dari standard status (kode/reason phrases)  */
    private const PHRASES = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    /** @var string */
    private $reasonPhrase = '';

    /** @var int */
    private $statusCode;

    /**
     * @param int $status Status code
     * @param array $headers Response headers
     * @param string|resource|StreamInterface|null $body Response body
     * @param string $version Protocol version
     * @param string|null $reason Reason phrase (ketika kosong default akan digunakan berdasarkan kode status)
     */
    public function make(
        int $status = 200,
        array $headers = [],
        $body = null,
        string $version = '1.1',
        string $reason = null
    ) {
        // Jika nilai body tidak ada
        // tunda inisialisasi stream, sampai Response::getBody()
        if ($body !== '' && $body !== null) {
            $this->stream = Stream::create($body);
        }

        $this->statusCode = $status;
        $this->setHeaders($headers);
        if (null === $reason && isset(self::PHRASES[$this->statusCode])) {
            $this->reasonPhrase = self::PHRASES[$status];
        } else {
            $this->reasonPhrase = $reason ?? '';
        }

        $this->protocol = $version;

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function withStatus(
        $code,
        $reasonPhrase = ''
    ): self {
        if (!is_int($code) && !is_string($code)) {
            throw new \InvalidArgumentException('Status code has to be an integer');
        }

        $code = (int) $code;
        if ($code < 100 || $code > 599) {
            throw new \InvalidArgumentException('Status code has to be an integer between 100 and 599');
        }

        $newObject = clone $this;
        $newObject->statusCode = $code;
        if ((
            $reasonPhrase === null ||
            $reasonPhrase === '')
            && isset(self::PHRASES[$newObject->statusCode])
        ) {
            $reasonPhrase = self::PHRASES[$newObject->statusCode];
        }
        $newObject->reasonPhrase = $reasonPhrase;

        return $newObject;
    }

    /*
     * RESPONSE LANGSUNG
     * */

    public function withView(string $path, array $data = [], TemplateFactory $templateFactory = null): ResponseInterface
    {
        var_dump($templateFactory);
        $view = new Page($path, $data);
        if ($templateFactory == null) {
            $resultHTML = $view->render(new TwigTemplateFactory());
        } else {
            var_dump($templateFactory);
            if ($templateFactory instanceof TemplateFactory) {
                $resultHTML = $view->render($templateFactory);
            }

            throw new \Exception('TemplateFactory must be instance of Document Factory');
        }

        $responseBody = \Nyholm\Psr7\Stream::create($resultHTML);
//        $responseBody = Stream::create($resultHTML);
        $response = $this->make(200)
            ->withBody($responseBody)
            ->withHeader('Content-Type', 'text/html');

        // jalan, tapi pakai nyholm
//        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
//        $responseBody = $psr17Factory->createStream($resultHTML);
//        $response = $psr17Factory->createResponse(200)->withBody($responseBody);
        return $response;
    }

    public function withJson($data)
    {
        $json = new Document($data);
        $resultJson = $json->result(new JsonFactory);
        $responseBody = \Nyholm\Psr7\Stream::create($resultJson);
        $response = $this->make(200)
            ->withBody($responseBody)
            ->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function withXml($data)
    {
        $xml = new Document($data);
        $resultXml = $xml->result(new XmlFactory());

        $responseBody = Stream::create($resultXml);
        $response = $this->make(200)
            ->withBody($responseBody)
            ->withHeader('Content-Type', 'application/xml');

        return $response;
    }

    public function withRedirect($location): void
    {
        die(var_dump($location));
        header('Location', $location);
    }

    public function withDownload()
    {
        // @todo
    }
}