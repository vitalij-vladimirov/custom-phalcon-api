<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Mvc\Model;
use Carbon\Carbon;
use Common\Regex;
use Common\Text;
use Common\Exception\DatabaseException;
use Common\Variable;

class BaseModel extends Model
{
    protected string $table;

    protected int $id;
    protected Carbon $created_at;
    protected Carbon $updated_at;

    public function getId(): int
    {
        if ($this->id === null) {
            throw new DatabaseException('Model has no id attribute.');
        }

        return (int)$this->id;
    }

    public function getCreatedAt(): Carbon
    {
        if ($this->created_at === null) {
            throw new DatabaseException('Model has no created_at attribute.');
        }

        if (!Variable::isDateTimeObject($this->created_at)) {
            $this->setCreatedAt((string)$this->created_at);
        }

        return $this->created_at;
    }

    public function getUpdatedAt(): Carbon
    {
        if ($this->updated_at === null) {
            throw new DatabaseException('Model has no updated_at attribute.');
        }

        if (!Variable::isDateTimeObject($this->updated_at)) {
            $this->setUpdatedAt((string)$this->updated_at);
        }

        return $this->updated_at;
    }

    protected function getTableName(): string
    {
        if (!empty($this->table)) {
            return $this->table;
        }

        $this->table = Text::toSnakeCase(get_class($this));

        if (Regex::isValidPattern($this->table, '/(_model)$/')) {
            $this->table = substr($this->table, 0, -6);
        }

        return $this->table;
    }

    protected function dateTimeToCarbon(string $dateTime): Carbon
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $dateTime);
    }

    protected function dateToCarbon(string $date): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $date);
    }

    private function setCreatedAt(string $createdAt): BaseModel
    {
        if ($this->created_at === null) {
            throw new DatabaseException('Model has no created_at attribute.');
        }

        $this->created_at = $this->dateTimeToCarbon($createdAt);

        return $this;
    }

    private function setUpdatedAt(string $updatedAt): BaseModel
    {
        if ($this->updated_at === null) {
            throw new DatabaseException('Model has no updated_at attribute.');
        }

        $this->updated_at = $this->dateTimeToCarbon($updatedAt);

        return $this;
    }
}
