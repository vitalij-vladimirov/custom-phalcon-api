<?php
declare(strict_types=1);

namespace Common\Interfaces;

interface ValidatorInterface
{
    public function validateData(array $data): void;
}
