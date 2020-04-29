<?php
declare(strict_types=1);

namespace Documentation\Entity;

use Common\BaseClass\BaseEntity;
use Common\Exception\LogicException;
use Common\Variable;

class ParameterDoc extends BaseEntity
{
    private ?string $type;
    private $example = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): ParameterDoc
    {
        if ($type !== null && !in_array($type, Variable::DEFAULT_VAR_TYPES, true)) {
            throw new LogicException('Unknown variable type \'' . $type . '\'.');
        }

        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getExample()
    {
        return $this->example;
    }

    /**
     * @param mixed $example
     *
     * @return ParameterDoc
     */
    public function setExample($example): ParameterDoc
    {
        $this->example = $example;
        return $this;
    }
}
