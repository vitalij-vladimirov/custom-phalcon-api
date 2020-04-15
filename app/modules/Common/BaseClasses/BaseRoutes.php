<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Common\ApiException\NotFoundApiException;
use Common\Entity\Route;
use Common\Exception\LogicException;
use Common\Interfaces\RoutesInterface;
use Common\Entity\RequestData;
use Common\Regex;

abstract class BaseRoutes extends Injectable implements RoutesInterface
{
    /**
     * These actions must be synchronized with Common/Interfaces actions.
     * Ensure to change actions values if changed them in interfaces or you
     * will get Exceptions.
     */
    private const DEFAULT_ACTIONS = [
        'request_mapper_action' => 'mapRequestToObject',
        'request_mapper_docs' => 'requestDocumentation',
        'response_mapper_action' => 'mapResponseToArray',
        'response_mapper_docs' => 'responseDocumentation',
        'resolver_action' => 'resolveParameter',
        'resolver_docs' => 'parameterDocumentation',
        'validator_action' => 'validateData',
    ];

    protected array $get = [];
    protected array $post = [];
    protected array $put = [];
    protected array $delete = [];

    protected RequestData $request;

    public function __construct(RequestData $request)
    {
        $this->request = $request;

        $this->routes(); // Load routes from extendable Routes class
    }

    abstract protected function routes(): void;

    public function get(): void
    {
        $route = $this->findRoute();

        /*
         * Route should be validated in development and testing environments
         * assuming that all errors will be corrected before and there is no
         * point to validate routes in production
         */
        if (APP_ENV !== 'production') {
            $this->validateRoute($route);
        }
    }

    private function findRoute(): Route
    {
        $method = $this->request->getMethod();
        $path = $this->request->getPath();

        if (isset($this->{$method}[$path])) {
            return $this->{$method}[$path];
        }

        if (count($this->{$method}) === 0) {
            throw new NotFoundApiException();
        }

        $pattern = ($this->request->getType() === RequestData::REQUEST_TYPE_API) ? '/^\/api' : '/^';
        foreach ($this->request->getParams() as $param) {
            $pattern .= '\/(' . $param . '|\{[a-zA-Z0-9_]{1,}\})';
        }
        $pattern .= '$/';

        foreach ($this->{$method} as $routePath => $route) {
            if (Regex::isValidPattern($routePath, $pattern)) {
                return $route;
            }
        }

        throw new NotFoundApiException();
    }

    private function validateRoute(Route $route): void
    {
        $documentationStatus = $route->getDocumentation()->getStatus();
        
        if (!class_exists($route->getController())) {
            throw new LogicException('Controller not found.');
        }

        if (!method_exists($route->getController(), $route->getAction())) {
            throw new LogicException('Controller action not found.');
        }

        if ($route->getValidator() !== null) {
            if (!class_exists($route->getValidator())) {
                throw new LogicException('Validator not found.');
            }

            if (!method_exists($route->getValidator(), self::DEFAULT_ACTIONS['validator_action'])) {
                throw new LogicException('Validator action not found.');
            }
        }

        if ($route->getRequestMapper() !== null) {
            if (!class_exists($route->getRequestMapper())) {
                throw new LogicException('Request mapper not found.');
            }

            if (!method_exists($route->getRequestMapper(), self::DEFAULT_ACTIONS['request_mapper_action'])) {
                throw new LogicException('Request mapper action not found.');
            }
            
            if ($documentationStatus === true
                && !method_exists($route->getRequestMapper(), self::DEFAULT_ACTIONS['request_mapper_docs'])
            ) {
                throw new LogicException('Request mapper documentation not found.');
            }
        }

        if ($route->getResponseMapper() !== null) {
            if (!class_exists($route->getResponseMapper())) {
                throw new LogicException('Response mapper not found.');
            }

            if (!method_exists($route->getResponseMapper(), self::DEFAULT_ACTIONS['response_mapper_action'])) {
                throw new LogicException('Response mapper action not found.');
            }

            if ($documentationStatus === true
                && !method_exists($route->getResponseMapper(), self::DEFAULT_ACTIONS['response_mapper_docs'])
            ) {
                throw new LogicException('Response mapper documentation not found.');
            }
        }

        if (count($route->getResolvers()) !== 0) {
            foreach ($route->getResolvers() as $parameter => $resolver) {
                if (!class_exists($resolver)) {
                    throw new LogicException('Parameter \'' . $parameter . '\' resolver not found.');
                }

                if (!method_exists($resolver, self::DEFAULT_ACTIONS['resolver_action'])) {
                    throw new LogicException('Parameter \'' . $parameter . '\' resolver action not found.');
                }

                if ($documentationStatus === true
                    && !method_exists($resolver, self::DEFAULT_ACTIONS['resolver_docs'])
                ) {
                    throw new LogicException('Parameter \'' . $parameter . '\' resolver documentation not found.');
                }
            }
        }
    }
}
