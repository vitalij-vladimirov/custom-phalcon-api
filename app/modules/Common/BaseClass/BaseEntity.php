<?php
declare(strict_types=1);

namespace Common\BaseClass;

use ReflectionMethod;
use Carbon\Carbon;
use Common\Exception\LogicException;
use Common\Json;
use Common\Regex;
use Common\Text;
use Common\Variable;
use ReflectionNamedType;
use Throwable;

abstract class BaseEntity
{
    public function toArray(
        bool $includeNulls = true,  // include empty values
        bool $emptyToNull = true,   // convert '' to null
        bool $timestamps = true     // convert DateTime to timestamps (true) or "Y-m-d H:i:s" (false)
    ): array {
        $array = [];

        foreach (get_class_methods($this) as $method) {
            if (!Regex::isMethodName($method, Regex::METHOD_TYPE_GET) || !$this->isMethodPublic($method)) {
                continue;
            }

            $content = $this->getMethodContent($method, $emptyToNull);

            if ($content === null && $includeNulls === false) {
                continue;
            }

            $variable = Text::uncamelizeMethod($method);
            $returnType = $this->getMethodReturnType($method);
            $contentType = Variable::getType($content);

            $isJson = Json::isJson($content);

            $isDefaultType = !$isJson
                && Variable::isDefaultType($returnType)
                && Variable::isDefaultType($contentType)
            ;

            if ($isDefaultType) {
                $array[$variable] = $content;
                continue;
            }

            if ($isJson) {
                $array[$variable] = Json::decode((string)$content);
                continue;
            }

            if (Variable::isDateTimeObject($content)) {
                $array[$variable] = Variable::convertTimeObjectToString($content, $timestamps);
                continue;
            }

            try {
                $array[$variable] = $content->toArray($includeNulls, $emptyToNull, $timestamps);
            } catch (Throwable $exception) {
                try {
                    $array[$variable] = $content->toArray();
                } catch (Throwable $exception) {
                    if ($includeNulls) {
                        $array[$variable] = null;
                    }
                }
            }
        }

        return $array;
    }

    public function toEntity($data): BaseEntity
    {
        if (Variable::isObject($data)) {
            try {
                $array = $data->toArray();
            } catch (Throwable $exception) {
                try {
                    foreach ($data as $key => $value) {
                        $array[Text::uncamelizeMethod($key)] = $value;
                    }
                } catch (Throwable $exception) {
                    throw new LogicException('Can\'t convert \'data\' object.');
                }
            }
        }

        if (Variable::isArray($data)) {
            $array = $data;
        }

        if (!isset($array)) {
            throw new LogicException('$data type must be array or object.');
        }

        foreach (get_class_methods($this) as $method) {
            if (!Regex:: isMethodName($method, Regex::METHOD_TYPE_SET) || !$this->isMethodPublic($method)) {
                continue;
            }

            $variable = Text::uncamelizeMethod($method);

            $content = $array[$variable];

            $parameters = $this->getMethodParams($method);

            if (count($parameters) !== 1) {
                continue;
            }

            $contentType = Variable::getType($content);
            $parameterType = $parameters[0]->getType()->getName();

            if ($parameterType === Variable::VAR_TYPE_BOOL && empty($content)) {
                $this->$method(false);
                continue;
            }

            if ($parameterType === Variable::VAR_TYPE_STRING
                && in_array($contentType, [Variable::VAR_TYPE_INT, Variable::VAR_TYPE_FLOAT], true)
            ) {
                $content = (string)$content;
                $contentType = Variable::getType($content);
            }

            if ($contentType === Variable::VAR_TYPE_INT && $parameterType === Carbon::class) {
                $this->$method(Carbon::createFromTimestamp($content));
                continue;
            }

            if ($contentType === Variable::VAR_TYPE_STRING && $parameterType === Carbon::class) {
                $this->$method(Carbon::createFromTimeString($content));
                continue;
            }

            try {
                $class = '\\' . $parameterType;

                if ($contentType === Variable::VAR_TYPE_OBJECT && class_exists($class)) {
                    $this->$method($content);
                    continue;
                }

                if ($contentType === Variable::VAR_TYPE_ARRAY && method_exists($class, 'toEntity')) {
                    $this->$method((new $class())->toEntity($content));
                    continue;
                }
            } catch (Throwable $exception) {
                continue;
            }

            $this->$method($content);
        }

        return $this;
    }

    /**
     * @param $method
     * @param bool $emptyToNull
     *
     * @return mixed|null
     */
    private function getMethodContent($method, bool $emptyToNull)
    {
        try {
            $content = $this->$method();
        } catch (Throwable $exception) {
            return null;
        }

        if ($emptyToNull && Variable::isString($content) && empty(trim($content))) {
            $content = null;
        }

        return $content;
    }

    private function getMethodReturnType(string $method): ?string
    {
        try {
            /** @var ReflectionNamedType $methodReturnType */
            $methodReturnType = (new ReflectionMethod($this, $method))->getReturnType();

            return $methodReturnType->getName();
        } catch (Throwable $exception) {
            return null;
        }
    }

    private function getMethodParams(string $method): array
    {
        try {
            return (new ReflectionMethod($this, $method))->getParameters();
        } catch (Throwable $exception) {
            return [];
        }
    }

    private function isMethodPublic(string $method): bool
    {
        try {
            return (new ReflectionMethod($this, $method))->isPublic();
        } catch (Throwable $exception) {
            return false;
        }
    }
}
