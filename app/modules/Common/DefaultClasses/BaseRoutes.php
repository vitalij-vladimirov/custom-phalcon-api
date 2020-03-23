<?php
declare(strict_types=1);

namespace Common\DefaultClasses;

use Common\Entity\RequestEntity;

class BaseRoutes
{
    private array $get = [];
    private array $post = [];
    private array $put = [];
    private array $delete = [];

    private const DEFAULT_ROUTES = [
        'get' => [
            'get' => '/api/([a-zA-Z0-9\_\-]+)/([0-9]+)',
            'getAll' => '/api/([a-zA-Z0-9\_\-]+)',
        ],
        'post' => [
            'create' => '/api/([a-zA-Z0-9\_\-]+)',
        ],
        'put' => [
            'update' => '/api/([a-zA-Z0-9\_\-]+)/([0-9]+)',
        ],
        'delete' => [
            'remove' => '/api/([a-zA-Z0-9\_\-]+)/([0-9]+)',
        ],
    ];

    private RequestEntity $request;

    public function __construct(RequestEntity $request)
    {
        $this->request = $request;

        if (count($this->{$this->request->getMethod()}) === 0) {
            $this->setDefaultRoute();
        }
    }

    private function setDefaultRoute(): void
    {
        foreach (self::DEFAULT_ROUTES[$this->request->getMethod()] as $action => $route) {
//            $pattern = '/' . str_replace('/', '|', $route) . '/';
//            $subject = str_replace('/', '|', $this->request->getPath());
//            if (preg_match($pattern, $subject)) {
//                dd($action);
//            }
        }

        dd(1);

//        $controllerName = Text::camelize($this->request->getParams()[0] ?? $this->request->getModule());
//        $controller = '\\' . $this->request->getModule() . '\Controller\\' . $controllerName . 'Controller';

//        $this->{$this->request->getMethod()} = [
//            (new RouteEntity())
//                ->setController()
//        ];
    }
}
