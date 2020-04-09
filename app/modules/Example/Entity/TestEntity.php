<?php
declare(strict_types=1);

namespace Example\Entity;

use Carbon\Carbon;
use Common\BaseClasses\BaseEntity;
use Common\Entity\DirectoryEntity;

class TestEntity extends BaseEntity
{
    private int $integer;
    private ?int $integerNullable;
    private float $float;
    private ?float $floatNullable;
    private string $string;
    private ?string $stringNullable;
    private bool $boolean;
    private ?bool $booleanNullable;
    private array $array;
    private DirectoryEntity $entity;
    private Carbon $object;

    public function getInteger(): int
    {
        return $this->integer;
    }

    public function setInteger(int $integer): TestEntity
    {
        $this->integer = $integer;
        return $this;
    }

    public function getIntegerNullable(): ?int
    {
        return $this->integerNullable;
    }

    public function setIntegerNullable(?int $integerNullable): TestEntity
    {
        $this->integerNullable = $integerNullable;
        return $this;
    }

    public function getFloat(): float
    {
        return $this->float;
    }

    public function setFloat(float $float): TestEntity
    {
        $this->float = $float;
        return $this;
    }

    public function getFloatNullable(): ?float
    {
        return $this->floatNullable;
    }

    public function setFloatNullable(?float $floatNullable): TestEntity
    {
        $this->floatNullable = $floatNullable;
        return $this;
    }

    public function getString(): string
    {
        return $this->string;
    }

    public function setString(string $string): TestEntity
    {
        $this->string = $string;
        return $this;
    }

    public function getStringNullable(): ?string
    {
        return $this->stringNullable;
    }

    public function setStringNullable(?string $stringNullable): TestEntity
    {
        $this->stringNullable = $stringNullable;
        return $this;
    }

    public function isBoolean(): bool
    {
        return $this->boolean;
    }

    public function setBoolean(bool $boolean): TestEntity
    {
        $this->boolean = $boolean;
        return $this;
    }

    public function getBooleanNullable(): ?bool
    {
        return $this->booleanNullable;
    }

    public function setBooleanNullable(?bool $booleanNullable): TestEntity
    {
        $this->booleanNullable = $booleanNullable;
        return $this;
    }

    public function getArray(): array
    {
        return $this->array;
    }

    public function setArray(array $array): TestEntity
    {
        $this->array = $array;
        return $this;
    }

    public function getEntity(): DirectoryEntity
    {
        return $this->entity;
    }

    public function setEntity(DirectoryEntity $entity): TestEntity
    {
        $this->entity = $entity;
        return $this;
    }

    public function getObject(): Carbon
    {
        return $this->object;
    }

    public function setObject(Carbon $object): TestEntity
    {
        $this->object = $object;
        return $this;
    }
}
