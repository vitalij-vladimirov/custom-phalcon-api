<?php
declare(strict_types=1);

namespace Common\Service;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Common\Entity\HttpResponse;
use Common\Exception\LogicException;
use Common\Json;
use Common\Regex;
use Common\Variable;

class HttpRequestManager extends Injectable
{
    public const DATA_TYPE_QUERY = 'query';
    public const DATA_TYPE_BODY = 'body';
    public const DATA_TYPE_JSON = 'json';
    public const DATA_TYPE_MULTI = 'multipart';

    private const ALLOWED_DATA_TYPES = [
        self::DATA_TYPE_QUERY,
        self::DATA_TYPE_BODY,
        self::DATA_TYPE_JSON,
        self::DATA_TYPE_MULTI,
    ];

    private Client $client;
    private string $appUri;

    public function __construct()
    {
        $this->client = new Client();

        $this->appUri = $this->di->get('config')->containerUrl;
    }

    public function getRequest(
        string $uri,
        array $data = [],
        string $dataType = self::DATA_TYPE_QUERY,
        int $timeout = 5,
        array $parameters = []
    ): HttpResponse {
        $this->validateDataType($dataType);

        $originalResponse = $this->client->request(
            'GET',
            $this->resolveUri($uri),
            $this->resolveRequestParameters($dataType, $data, $timeout, $parameters)
        );

        return $this->resolveResponse($originalResponse);
    }

    public function postRequest(
        string $uri,
        array $data = [],
        string $dataType = self::DATA_TYPE_JSON,
        int $timeout = 5,
        array $parameters = []
    ): HttpResponse {
        $this->validateDataType($dataType);

        $originalResponse = $this->client->request(
            'POST',
            $this->resolveUri($uri),
            $this->resolveRequestParameters($dataType, $data, $timeout, $parameters)
        );

        return $this->resolveResponse($originalResponse);
    }

    public function putRequest(
        string $uri,
        array $data = [],
        string $dataType = self::DATA_TYPE_JSON,
        int $timeout = 5,
        array $parameters = []
    ): HttpResponse {
        $this->validateDataType($dataType);

        $originalResponse = $this->client->request(
            'PUT',
            $this->resolveUri($uri),
            $this->resolveRequestParameters($dataType, $data, $timeout, $parameters)
        );

        return $this->resolveResponse($originalResponse);
    }

    public function deleteRequest(
        string $uri,
        array $data = [],
        string $dataType = self::DATA_TYPE_QUERY,
        int $timeout = 5,
        array $parameters = []
    ): HttpResponse {
        $this->validateDataType($dataType);

        $originalResponse = $this->client->request(
            'DELETE',
            $this->resolveUri($uri),
            $this->resolveRequestParameters($dataType, $data, $timeout, $parameters)
        );

        return $this->resolveResponse($originalResponse);
    }

    private function resolveUri($uri): string
    {
        if (Regex::isValidPattern($uri, '/^(https:|http:|ftp:)/')) {
            return $uri;
        }

        if ($uri[0] === '/') {
            return $this->appUri . $uri;
        }

        return $this->appUri . '/' . $uri;
    }

    private function validateDataType(string $dataType): void
    {
        if (!in_array($dataType, self::ALLOWED_DATA_TYPES, true)) {
            throw new LogicException('Incorrect \'dataType\' value.');
        }
    }

    private function resolveRequestParameters(
        string $dataType = self::DATA_TYPE_JSON,
        array $data = [],
        int $timeout = 5,
        array $additionalParameters = []
    ): array {
        $parameters = [
            $dataType => $data,
            'connect_timeout' => $timeout,
            'http_errors' => true,
            'allow_redirects' => [
                'max'             => 2,
                'strict'          => false,
                'referer'         => false,
                'protocols'       => ['http', 'https', 'ftp'],
//                'on_redirect'     => null,
                'track_redirects' => true
            ],
            'headers' => [
                'User-Agent' => getenv('APP_USER_AGENT'),
            ],
        ];

        foreach ($additionalParameters as $key => $parameter) {
            if (Variable::isArray($parameter)) {
                foreach ($parameter as $paramKey => $paramValue) {
                    $parameters[$key][$paramKey] = $paramValue;
                }

                continue;
            }

            $parameters[$key] = $parameter;
        }

        return $parameters;
    }

    private function resolveResponse(ResponseInterface $originalResponse): HttpResponse
    {
        [$contentType] = explode(';', $originalResponse->getHeaderLine('Content-Type'));

        $httpResponse = (new HttpResponse())
            ->setStatusCode($originalResponse->getStatusCode())
            ->setContentType($contentType)
            ->setContent($originalResponse->getBody()->getContents())
            ->setSize($originalResponse->getBody()->getSize())
            ->setHeaders($originalResponse->getHeaders())
            ->setResponsePhrase($originalResponse->getReasonPhrase())
        ;

        if ($httpResponse->getContentType() === 'application/json'
            || Json::isJson($httpResponse->getContent())
        ) {
            $httpResponse->setJsonContent(Json::decode($httpResponse->getContent()));
        }

        return $httpResponse;
    }
}
