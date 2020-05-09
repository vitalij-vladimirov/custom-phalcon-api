<?php
declare(strict_types=1);

namespace Common\Interfaces;

interface ResponseMapperInterface
{
    public function mapResponseToArray($object): array;
//    public function responseDocumentation(): ?ResponseDoc;
}
