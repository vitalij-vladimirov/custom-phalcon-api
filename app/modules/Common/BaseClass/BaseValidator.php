<?php
declare(strict_types=1);

namespace Common\BaseClass;

use Phalcon\Messages\Messages;
use Phalcon\Validation;
use Common\Service\Injectable;
use Common\ApiException\BadRequestApiException;

abstract class BaseValidator extends Injectable
{
    protected const STRING_LENGTH = '255';

    abstract public function validationSchema(Validation $validation): void;

    public function runValidationAndGetMessages(array $data = []): Messages
    {
        $validation = new Validation();

        // Get validation schema
        $this->validationSchema($validation);

        // Run validation and collect messages
        return $validation->validate($data, null);
    }

    public function runValidationAndThrowException(array $data = []): void
    {
        $messages = $this->runValidationAndGetMessages($data);

        // Throw API exception if any errors found
        if ($messages->count() !== 0) {
            $messageData = [];
            foreach ($messages as $message) {
                $messageData[] = [
                    'field' => $message->getField(),
                    'message' => $message->getMessage(),
                ];
            }

            throw new BadRequestApiException(
                'Bad request',
                'bad_request',
                $messageData
            );
        }
    }
}
