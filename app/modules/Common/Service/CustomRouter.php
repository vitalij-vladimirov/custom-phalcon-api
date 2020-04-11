<?php
declare(strict_types=1);

namespace Common\Service;

use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;
use Mvc\RouterInterface;
use Common\ApiException\ApiException;
use Common\ApiException\NotFoundApiException;
use Common\Entity\RequestEntity;
use Common\File;
use Common\Text;
use Common\Variable;

class CustomRouter implements RouterInterface
{
    private Micro $app;

    public function getRoutes(Micro $app): Micro
    {
        $this->app = $app;

        try {
            $request = $this->getRequest();
            $response = $this->runRequest($request);
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

        return $response;
    }

    private function getRequest(): RequestEntity
    {
        $modulesDir = $this->app->di->get('config')->application->modulesDir;

        list($urlPath) = explode('?', $this->app->request->getURI());

        $request = (new RequestEntity())
            ->setMethod(Text::lower($this->app->request->getMethod()))
            ->setQuery(Variable::restoreArrayTypes($this->app->request->getQuery()))
            ->setPath($urlPath)
        ;

        $urlSplitter = explode('/', $urlPath);

        if (count($urlSplitter) < 2 || (count($urlSplitter) < 3 && $urlSplitter[1] === $request::REQUEST_TYPE_API)) {
            throw new NotFoundApiException();
        }

        if ($urlSplitter[1] === $request::REQUEST_TYPE_API) {
            $request
                ->setType($request::REQUEST_TYPE_API)
                ->setModule(Text::camelize($urlSplitter[2]))
                ->setParams(Variable::restoreArrayTypes(array_slice($urlSplitter, 3)));
        } else {
            $request
                ->setType($request::REQUEST_TYPE_VIEW)
                ->setModule(Text::camelize($urlSplitter[1]))
                ->setParams(Variable::restoreArrayTypes(array_slice($urlSplitter, 2)));
        }

        if (!File::exists($modulesDir . '/' . $request->getModule())) {
            throw new NotFoundApiException();
        }

        return $request;
    }

    private function runRequest(RequestEntity $request): Micro
    {
        $routesClass = '\\' . $request->getModule() . '\\Config\Routes';
        if (!class_exists($routesClass)) {
            throw new NotFoundApiException();
        }

        $app = $this->app;
        $responseData = (new $routesClass($request))->get();

        $this->app->{$request->getMethod()}(
            $request->getPath(),
            function () use ($app, $responseData) {
                // TODO: describe different data types
                $app->response
                    ->setJsonContent($responseData)
                    ->send();
            }
        );

        return $this->app;
    }
}
