<?php
declare(strict_types=1);

namespace Common\BaseValidator;

use Phalcon\Messages\Message as PhalconMessage;

class Message extends PhalconMessage
{
    private ?string $errorCode;

    public function __construct(string $field, string $message, string $errorCode = null)
    {
        parent::__construct($message, $field, 'Custom');

        $this->errorCode = $errorCode;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }
}
