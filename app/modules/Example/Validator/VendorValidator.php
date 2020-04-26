<?php
declare(strict_types=1);

namespace Example\Validator;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Common\BaseClass\BaseValidator;
use Common\BaseValidator\EnvironmentValidator;

class VendorValidator extends BaseValidator
{
    private EnvironmentValidator $environmentValidator;

    public function __construct(EnvironmentValidator $environmentValidator)
    {
        $this->environmentValidator = $environmentValidator;
    }

    public function validationSchema(Validation $validation): void
    {
        $validation
            ->add(['lib_name', 'lib_url', 'version', 'environment', 'description'], new PresenceOf())
            ->add('lib_name', new StringLength(['max' => self::STRING_LENGTH]))
            ->add('lib_url', new StringLength(['max' => self::STRING_LENGTH]))
            ->add('version', new StringLength(['max' => 10]))
        ;

        // Include environment validation
        $this->environmentValidator->validationSchema($validation);
    }
}
