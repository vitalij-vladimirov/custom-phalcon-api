<?php
declare(strict_types=1);

namespace Common\BaseClass;

use Documentation\Entity\ParameterDoc;
use Authorization\Service\PermissionsManager;
use Common\BaseMapper\BasePaginationFilterRequestMapper;
use Common\Exception\ForbiddenException;
use Common\Exception\LogicException;
use Common\ApiException\BadRequestApiException;
use Common\ApiException\NotFoundApiException;
use Common\ApiException\UnauthorizedApiException;
use Common\BaseMapper\BaseListResponseMapper;
use Common\BaseMapper\BasePaginatedResponseMapper;
use Common\Service\Injectable;
use Common\Entity\Route;
use Common\Interfaces\RequestMapperInterface;
use Common\Interfaces\ResponseMapperInterface;
use Common\Entity\RequestData;
use Common\Variable;
use Common\Regex;

abstract class BaseRoutes extends Injectable
{
    protected array $get = [];
    protected array $post = [];
    protected array $put = [];
    protected array $patch = [];
    protected array $delete = [];

    protected RequestData $requestData;
    protected string $routePath;
    protected Route $route;

    protected PermissionsManager $permissionsManager;

    private const DEFAULT_ACTIONS = [
        'request_mapper_action' => 'mapRequestToObject',
        'request_mapper_docs' => 'requestDocumentation',
        'response_mapper_action' => 'mapResponseToArray',
        'response_mapper_docs' => 'responseDocumentation',
        'resolver_action' => 'resolveParameter',
        'resolver_docs' => 'parameterDocumentation',
        'validator_action' => 'validationSchema',
    ];

    public function __construct(PermissionsManager $permissionsManager)
    {
        $this->permissionsManager = $permissionsManager;

        $this->routes(); // Loading routes from extendable Routes class
    }

    abstract protected function routes(): void;

    /**
     * @param RequestData $requestData
     *
     * @return mixed
     *
     * @throws LogicException
     * @throws BadRequestApiException
     * @throws NotFoundApiException
     * @throws UnauthorizedApiException
     * @throws ForbiddenException
     */
    public function getRoute(RequestData $requestData)
    {
        $this->requestData = $requestData;

        $this->findRoute();
        $this->validateRoute();

        return $this->runRoute();
    }

    /**
     * @throws NotFoundApiException
     */
    private function findRoute(): void
    {
        $method = $this->requestData->getMethod();
        $path = $this->requestData->getPath();

        if (count($this->{$method}) === 0) {
            throw new NotFoundApiException();
        }

        if (isset($this->{$method}[$path])) {
            $this->routePath = $path;
            $this->route = $this->{$method}[$path];

            return;
        }

        $pattern = ($this->requestData->getType() === RequestData::REQUEST_TYPE_API) ? '/^\/api' : '/^';
        foreach ($this->requestData->getParams() as $param) {
            $pattern .= '\/(' . $param . '|\{[a-zA-Z0-9_]{1,}\})';
        }
        $pattern .= '$/';

        foreach ($this->{$method} as $routePath => $route) {
            if (Regex::isValidPattern($routePath, $pattern)) {
                $this->routePath = $routePath;
                $this->route = $route;

                return;
            }
        }

        throw new NotFoundApiException();
    }

    /**
     * @throws BadRequestApiException
     * @throws ForbiddenException
     * @throws LogicException
     * @throws UnauthorizedApiException
     */
    private function validateRoute(): void
    {
        /*
         * Route should be validated in development and/or testing environments
         * assuming that all errors will be corrected and there is no point to
         * validate routes logic in production
         */
        if (APP_ENV !== 'production') {
            $this->validateRouteLogic();
        }

        if ($this->route->isSecured() && !$this->permissionsManager->isAuthorized()) {
            throw new UnauthorizedApiException();
        }

        if (count($this->route->getPermissions()) !== 0
            && !$this->permissionsManager->isAllowed($this->route->getPermissions())
        ) {
            throw new ForbiddenException();
        }

        if ($this->route->getStatus() !== Route::STATUS_ACTIVE) {
            switch ($this->route->getStatus()) {
                case Route::STATUS_DEPRECATED:
                    throw new BadRequestApiException(
                        'Method deprecated. Check documentation for more information.'
                    );
                case Route::STATUS_MOVED:
                    throw new BadRequestApiException(
                        'Method moved. Check documentation for more information.'
                    );
                default:
                    throw new BadRequestApiException(
                        'Method is not active. Check documentation or contact admin for more information.'
                    );
            }
        }
    }

    /**
     * @throws LogicException
     */
    private function validateRouteLogic(): void
    {
        if (!class_exists($this->route->getController())) {
            throw new LogicException('Controller not found.');
        }

        if (!method_exists($this->route->getController(), $this->route->getAction())) {
            throw new LogicException('Controller action not found.');
        }

        if ($this->route->getValidator() !== null) {
            if (!class_exists($this->route->getValidator())) {
                throw new LogicException('Validator not found.');
            }

            if (!method_exists($this->route->getValidator(), self::DEFAULT_ACTIONS['validator_action'])) {
                throw new LogicException('Validator action not found.');
            }
        }

        if ($this->route->getRequestMapper() !== null) {
            if (!class_exists($this->route->getRequestMapper())) {
                throw new LogicException('Request mapper not found.');
            }

            if (!in_array(
                RequestMapperInterface::class,
                class_implements($this->route->getRequestMapper()),
                true
            )) {
                throw new LogicException('Request mapper must implement RequestMapperInterface.');
            }
        }

        if ($this->route->getResponseMapper() !== null) {
            if (!class_exists($this->route->getResponseMapper())) {
                throw new LogicException('Response mapper not found.');
            }

            if (!in_array(
                ResponseMapperInterface::class,
                class_implements($this->route->getResponseMapper()),
                true
            )) {
                throw new LogicException('Request mapper must implement ResponseMapperInterface.');
            }
        }

        if (count($this->route->getResolvers()) !== 0) {
            foreach ($this->route->getResolvers() as $parameter => $resolver) {
                if (Variable::isObject($resolver, ParameterDoc::class)) {
                    continue;
                }

                if (!class_exists($resolver)) {
                    throw new LogicException('Parameter \'' . $parameter . '\' resolver not found.');
                }
            }
        }
    }

    /**
     * @return mixed
     * @throws BadRequestApiException
     * @throws LogicException
     */
    private function runRoute()
    {
        // Get collected request data
        $data = $this->requestData->getData();

        // Validate data provider with request
        $this->validateData($data);

        // Collect raw path parameters
        $pathParameters = $this->collectPathParameters();

        // Convert raw path parameters to parameters that will be sent to controller
        $parameters = $this->resolveParameters($pathParameters);

        // Map request data to object
        $requestData = $this->resolveRequest($data);

        // Send collected data and parameters to controller and collect response
        $response = $this->callController($parameters, $requestData, $pathParameters);

        // Map response object to array
        return $this->resolveResponse($response);
    }

    /**
     * @param array $data
     * @throws BadRequestApiException
     */
    private function validateData(array $data): void
    {
        if ($this->route->getValidator()) {
            /** @var BaseValidator $validator */
            $validator = $this->inject($this->route->getValidator());
            $validator->runValidationAndThrowException($data);
        }
    }

    private function resolveParameters(array $pathParameters): array
    {
        $resolvers = [];

        if (count($this->route->getResolvers())) {
            foreach ($this->route->getResolvers() as $parameterKey => $parameterData) {
                if (!isset($pathParameters[$parameterKey])) {
                    throw new LogicException('Parameter \'' . $parameterKey . '\' not found.');
                }

                if (Variable::isObject($parameterData, ParameterDoc::class)) {
                    $resolvers[] = $pathParameters[$parameterKey];
                    continue;
                }

                /** @var BaseResolver $resolver */
                $resolver = $this->inject($parameterData);

                $resolvers[] = $resolver->resolveParameter($pathParameters[$parameterKey]);
            }
        }

        return $resolvers;
    }

    /**
     * @param array $data
     * @return mixed|null
     */
    private function resolveRequest(array $data)
    {
        if ($this->route->getRequestMapper()) {
            /** @var RequestMapperInterface $requestMapper */
            $requestMapper = $this->inject($this->route->getRequestMapper());

            if ($this->route->getEndpointType() === Route::ENDPOINT_TYPE_PAGINATED) {
                return (new BasePaginationFilterRequestMapper($requestMapper))
                    ->mapRequestToObject($data)
                ;
            }

            return $requestMapper->mapRequestToObject($data);
        }

        return null;
    }

    private function collectPathParameters(): array
    {
        $parameters = [];

        $routePathExploded = explode('/', $this->routePath);
        $routePaths = array_slice(
            $routePathExploded,
            count($routePathExploded) - count($this->requestData->getParams())
        );

        foreach ($routePaths as $key => $value) {
            if ($value !== $this->requestData->getParam($key)
                && Regex::isValidPattern($value, '/^\{[a-zA-Z0-9_]{1,}\}$/')
            ) {
                $valueKey = str_replace(['{', '}'], '', $value);
                $parameters[$valueKey] = $this->requestData->getParam($key);
            }
        }

        return $parameters;
    }

    private function callController(array $parameters, $requestData, array $pathParameters)
    {
        $controller = $this->inject($this->route->getController());
        $action = $this->route->getAction();

        $parameters = $parameters ?? $pathParameters ?? [];

        if (count($parameters) !== 0) {
            if ($requestData !== null) {
                $parameters[] = $requestData;
            }

            return call_user_func_array([$controller, $action], $parameters);
        }

        if ($requestData !== null) {
            return $controller->$action($requestData);
        }

        return $controller->$action();
    }

    private function resolveResponse($response)
    {
        if ($this->route->getResponseMapper()) {
            /** @var ResponseMapperInterface $responseMapper */
            $responseMapper = $this->inject($this->route->getResponseMapper());

            switch ($this->route->getEndpointType()) {
                case Route::ENDPOINT_TYPE_PAGINATED:
                    return (new BasePaginatedResponseMapper($responseMapper))
                        ->mapResponseToArray($response)
                    ;
                case Route::ENDPOINT_TYPE_LIST:
                    return (new BaseListResponseMapper($responseMapper))
                        ->mapResponseToArray($response)
                    ;
                default:
                    return $responseMapper->mapResponseToArray($response);
            }
        }

        return $response;
    }
}
