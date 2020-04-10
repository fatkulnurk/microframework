<?php
//declare(strict_types=1);

namespace Fatkulnurk\Microframework\Http\Message;

use Fatkulnurk\Microframework\App;
use Fatkulnurk\Microframework\Core\Singleton;
use Fatkulnurk\Microframework\Http\Data\Document\Document;
use Fatkulnurk\Microframework\Http\Data\Document\DocumentFactory;
use Fatkulnurk\Microframework\Http\Data\Document\JsonFactory;
use Fatkulnurk\Microframework\Http\Data\Document\XmlFactory;
use Fatkulnurk\Microframework\Http\Data\View\Page;
use Fatkulnurk\Microframework\Http\Data\View\TemplateFactory;
use Fatkulnurk\Microframework\Http\Data\View\TwigTemplateFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use function is_int;
use function is_string;

final class Response implements ResponseInterface
{
//    use MessageTrait;
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

    public function withView(string $path, array $data = [], TemplateFactory $templateFactory = null, int $status = 200): ResponseInterface
    {
        // var_dump($templateFactory);
        $view = new Page($path, $data);
        if ($templateFactory == null) {
            $resultHTML = $view->render(new TwigTemplateFactory());
        } else {
            // var_dump($templateFactory);
            if ($templateFactory instanceof TemplateFactory) {
                $resultHTML = $view->render($templateFactory);
            } else {
                throw new \Exception('TemplateFactory must be instance of Document Factory');
            }
        }

//        $responseBody = \Nyholm\Psr7\Stream::create($resultHTML);
        $responseBody = Stream::create($resultHTML);
        $response = $this->make($status)
            ->withBody($responseBody)
            ->withHeader('Content-Type', 'text/html');

        // jalan, tapi pakai nyholm
//        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
//        $responseBody = $psr17Factory->createStream($resultHTML);
//        $response = $psr17Factory->createResponse(200)->withBody($responseBody);
        return $response;
    }

    public function withJson($data, $status = 200)
    {
        $json = new Document($data);
        $resultJson = $json->result(new JsonFactory);
        $responseBody = \Nyholm\Psr7\Stream::create($resultJson);
        $response = $this->make($status)
            ->withBody($responseBody)
            ->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function withXml($data, $status = 200)
    {
        $xml = new Document($data);
        $resultXml = $xml->result(new XmlFactory());

        $responseBody = Stream::create($resultXml);
        $response = $this->make($status)
            ->withBody($responseBody)
            ->withHeader('Content-Type', 'application/xml');

        return $response;
    }

    public function withRedirect($location, $status = 200)
    {
        $response = $this->make($status)
            ->withHeader('Location', $location);
        return $response;
        //header('Location', $location);
    }

    public function withDownload($filename = '', $pathLocation)
    {
        $data = $pathLocation;

        if ($filename == '' || $data == '') {
            return false;
        }

        if (!file_exists($data)) {
            return false;
        }

        if (false === strpos($filename, '.')) {
            return false;
        }

        $extension = strtolower(pathinfo(basename($filename), PATHINFO_EXTENSION));

        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            // ms office
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        if (!isset($mime_types[$extension])) {
            $mime = 'application/octet-stream';
        } else {
            $mime = ( is_array($mime_types[$extension]) ) ? $mime_types[$extension][0] : $mime_types[$extension];
        }

        if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
            header('Content-Type: "' . $mime . '"');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header("Content-Transfer-Encoding: binary");
            header('Pragma: public');
            header("Content-Length: " . filesize($data));
        } else {
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: " . $mime, true, 200);
            header('Content-Length: ' . filesize($data));
            header('Content-Disposition: attachment; filename=' . $filename);
            header("Content-Transfer-Encoding: binary");
        }
        try {
            readfile(App::getInstance()->getPath() . $data);
//            readfile(App::getInstance()->getConfig('path_public') . $data);
        } catch (\Exception $e) {
            echo $e;
            throw new \Exception("Path Not Found");
        }
        exit;
    }
}
