<?php
declare(strict_types=1);

namespace Example\Validator;

use Common\BaseValidator\Message;
use Example\Config\ErrorCodes;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Callback;
use Common\BaseClass\BaseValidator;
use Common\BaseValidator\EnvironmentValidator;

class VendorValidator extends BaseValidator
{
    private EnvironmentValidator $environmentValidator;

    public function __construct(EnvironmentValidator $environmentValidator)
    {
        $this->environmentValidator = $environmentValidator;
    }

    public function validationSchema(Validation $validation, array $data = []): void
    {
        $validation
            ->add(['lib_name', 'lib_url', 'version', 'environment', 'description'], new PresenceOf())
            ->add('lib_name', new StringLength(['max' => self::STRING_MAX_LENGTH]))
            ->add('lib_url', new StringLength(['max' => self::STRING_MAX_LENGTH]))
            ->add('version', new StringLength(['max' => 10]))
            ->add('lib_name', new Callback([
                'callback' => static function ($data) use ($validation) {
                    if (!isset($data['lib_name'], $data['lib_url'])) {
                        return true;
                    }

                    if ($data['lib_name'] === $data['lib_url']) {
                        $validation->appendMessage(
                            new Message(
                                'lib_name',
                                'Fields \'lib_name\' and \'lib_url\' should not match each other.',
                                ErrorCodes::FIELDS_MATCH
                            )
                        );

                        return true;
                    }

                    return true;
                },
                'allowEmpty' => true,
            ]))
        ;

        // Include environment validation
        $this->environmentValidator->validationSchema($validation);
    }
}
