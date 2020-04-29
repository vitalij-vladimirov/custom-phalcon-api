<?php
declare(strict_types=1);

namespace Common\Service;

use Phalcon\Http\Response;
use Phalcon\Mvc\Micro;
use Mvc\RouterInterface;
use Common\Exception\LogicException;
use Common\Interfaces\RoutesInterface;
use Common\ApiException\ApiException;
use Common\ApiException\NotFoundApiException;
use Common\Entity\RequestData;
use Common\File;
use Common\Text;
use Throwable;

final class CustomRouter extends Injectable implements RouterInterface
{
    public function getRoutes(Micro $app): void
    {
        try {
            $this->runRequest($this->getRequest(), $app);
        } catch (ApiException $exception) {
            $response = [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ];

            if (count($exception->getData())) {
                $response['data'] = $exception->getData();
            }

            (new Response())
                ->setStatusCode($exception->getHttpCode(), $exception->getMessage())
                ->setJsonContent($response)
                ->send()
            ;

            exit;
        }
    }

    private function getRequest(): RequestData
    {
        $modulesDir = $this->di->get('config')->application->modulesDir;

        [$urlPath] = explode('?', $this->request->getURI());

        $request = (new RequestData())
            ->setMethod(Text::lower($this->request->getMethod()))
            ->setData($this->collectData())
            ->setPath($urlPath)
        ;

        $urlSplitter = explode('/', $urlPath);

        if (count($urlSplitter) < 2
            || (count($urlSplitter) < 3 && $urlSplitter[1] === RequestData::REQUEST_TYPE_API)
        ) {
            throw new NotFoundApiException();
        }

        if ($urlSplitter[1] === RequestData::REQUEST_TYPE_API) {
            $request
                ->setType(RequestData::REQUEST_TYPE_API)
                ->setModule(Text::toPascalCase($urlSplitter[2]))
                ->setParams(array_slice($urlSplitter, 2))
            ;
        } else {
            if (empty($urlSplitter[1])) {
                $urlSplitter[1] = 'index';
            }

            $request
                ->setType(RequestData::REQUEST_TYPE_VIEW)
                ->setModule(Text::toPascalCase($urlSplitter[1]))
                ->setParams(array_slice($urlSplitter, 1))
            ;
        }

        if (!File::exists($modulesDir . '/' . $request->getModule())) {
            throw new NotFoundApiException();
        }

        return $request;
    }

    private function collectData(): array
    {
        if ($this->request->getMethod() === 'GET') {
            try {
                $queryData = $this->request->getQuery();

                if (count($queryData) !== 0) {
                    return $queryData;
                }
            } catch (Throwable $throwable) {
                return [];
            }
        }

        if (in_array(
            $this->request->getContentType(),
            ['application/json', 'application/json;charset=UTF-8'],
            true
        )) {
            try {
                return $this->request->getJsonRawBody(true);
            } catch (Throwable $throwable) {
                return [];
            }
        }

        if ($this->request->getMethod() === 'POST') {
            try {
                return $this->request->getPost();
            } catch (Throwable $throwable) {
                return [];
            }
        }

        if ($this->request->getMethod() === 'PUT') {
            try {
                return $this->request->getPut();
            } catch (Throwable $throwable) {
                return [];
            }
        }

        return [];
    }

    private function runRequest(RequestData $request, Micro $app): Micro
    {
        if ($request->getParam(1) !== null) {
            $pathRoutes = Text::toPascalCase($request->getParam(1));
            $routesClass =  $routesClass = '\\' . $request->getModule() . '\\Route\\' . $pathRoutes . 'Routes';
        }

        if (!isset($routesClass) || !class_exists($routesClass)) {
            $routesClass = '\\' . $request->getModule() . '\\Route\Routes';
        }

        if (!class_exists($routesClass)) {
            throw new NotFoundApiException();
        }

        if (!in_array(RoutesInterface::class, class_implements($routesClass), true)) {
            throw new LogicException('Routes class must implement RoutesInterface.');
        }

        /** @var RoutesInterface $routes */
        $routes = $this->inject($routesClass);

        $response = $routes->get($request);

        $app->{$request->getMethod()}(
            $request->getPath(),
            function () use ($app, $response) {
                // TODO: describe different data types
                $app->response
                    ->setJsonContent($response)
                    ->send()
                ;
            }
        );

        return $app;
    }
}
