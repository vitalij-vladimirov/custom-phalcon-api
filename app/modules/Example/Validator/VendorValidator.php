<?php
declare(strict_types=1);

namespace Example\Validator;

use Common\BaseClasses\BaseValidator;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

class VendorValidator extends BaseValidator
{
    public function validateData(array $data): void
    {
        $this
            ->add(['lib_name', 'lib_url', 'version', 'environment', 'description'], new PresenceOf())
            ->add('lib_name', new StringLength(['max' => self::STRING_LENGTH]))
            ->add('lib_url', new StringLength(['max' => self::STRING_LENGTH]))
            ->add('version', new StringLength(['max' => 10]))
            ->add('environment', new StringLength(['max' => 20]))
            ->add('environment', new InclusionIn([
                'domain' => ['development', 'production'],
                'strict' => true,
            ]))
        ;
    }
}
