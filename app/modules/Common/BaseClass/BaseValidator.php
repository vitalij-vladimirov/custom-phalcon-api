<?php
declare(strict_types=1);

namespace Common\BaseClass;

use Phalcon\Messages\MessageInterface;
use Phalcon\Messages\Messages;
use Phalcon\Messages\Message as PhalconMessage;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Alnum;
use Phalcon\Validation\Validator\Alpha;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\Callback;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\CreditCard;
use Phalcon\Validation\Validator\Date;
use Phalcon\Validation\Validator\Digit;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\ExclusionIn;
use Phalcon\Validation\Validator\File;
use Phalcon\Validation\Validator\File\MimeType;
use Phalcon\Validation\Validator\File\Resolution\Equal as ResolutionEqual;
use Phalcon\Validation\Validator\File\Resolution\Max as ResolutionMax;
use Phalcon\Validation\Validator\File\Resolution\Min as ResolutionMin;
use Phalcon\Validation\Validator\File\Size\Equal as SizeEqual;
use Phalcon\Validation\Validator\File\Size\Max as SizeMax;
use Phalcon\Validation\Validator\File\Size\Min as SizeMin;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Ip;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\StringLength\Max as StringLengthMax;
use Phalcon\Validation\Validator\StringLength\Min as StringLengthMin;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\Url;
use Common\Config\DefaultErrorCodes;
use Common\Service\Injectable;
use Common\ApiException\BadRequestApiException;
use Common\BaseValidator\Message as ValidationMessage;
use Common\Text;

abstract class BaseValidator extends Injectable
{
    public const STRING_MAX_LENGTH = 255;

    abstract public function validationSchema(Validation $validation, array $data = []): void;

    public function runValidationAndGetMessages(array $data = []): Messages
    {
        $validation = new Validation();

        // Get validation schema
        $this->validationSchema($validation, $data);

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
                    'code' => $this->getCode($message),
                    'message' => str_replace(
                        [$message->getField(), '\'\'', '...', '..'],
                        ['\'' . Text::toSnakeCase($message->getField()) . '\'', '\'', '.', '.'],
                        $message->getMessage() . '.'
                    ),
                ];
            }

            throw new BadRequestApiException(
                'Invalid parameters.',
                DefaultErrorCodes::INVALID_PARAMETERS,
                $messageData
            );
        }
    }

    /**
     * @param MessageInterface|PhalconMessage|ValidationMessage $message
     * @return string
     */
    private function getCode(MessageInterface $message): string
    {
        if ($message instanceof ValidationMessage && $message->getErrorCode() !== null) {
            return $message->getErrorCode();
        }

        switch ($message->getType()) {
            case PresenceOf::class:
                return DefaultErrorCodes::IS_REQUIRED;

            case InclusionIn::class:
            case ExclusionIn::class:
            case Between::class:
            case Identical::class:
            case CreditCard::class:
                return DefaultErrorCodes::UNSUPPORTED_VALUE;

            case StringLengthMin::class:
            case StringLengthMax::class:
            case StringLength::class:
                return DefaultErrorCodes::INVALID_LENGTH;

            case Date::class:
            case Regex::class:
            case Url::class:
            case Ip::class:
                return DefaultErrorCodes::INVALID_FORMAT;

            case Email::class:
                return DefaultErrorCodes::BAD_EMAIL;

            case Confirmation::class:
                return DefaultErrorCodes::VALUES_DOES_NOT_MATCH;

            case Numericality::class:
            case Digit::class:
            case Alpha::class:
            case Alnum::class:
                return DefaultErrorCodes::INVALID_TYPE;

            case Uniqueness::class:
                return DefaultErrorCodes::DUPLICATE;

            case File::class:
                return DefaultErrorCodes::INVALID_FILE;

            case MimeType::class:
                return DefaultErrorCodes::INVALID_MIME_TYPE;

            case SizeEqual::class:
            case SizeMin::class:
            case SizeMax::class:
                return DefaultErrorCodes::INVALID_FILE_SIZE;

            case ResolutionEqual::class:
            case ResolutionMin::class:
            case ResolutionMax::class:
                return DefaultErrorCodes::INVALID_RESOLUTION;

            case Callback::class:
                return DefaultErrorCodes::VALIDATION_ERROR;

            default:
                return DefaultErrorCodes::UNKNOWN_ERROR;
        }
    }
}
