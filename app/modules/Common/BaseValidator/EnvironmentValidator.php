<?php
declare(strict_types=1);

namespace Common\BaseValidator;

use Phalcon\Validation;
use Phalcon\Validation\Validator\InclusionIn;
use Common\BaseClass\BaseValidator;

class EnvironmentValidator extends BaseValidator
{
    public function validationSchema(Validation $validation): void
    {
        $validation
            ->add('environment', new InclusionIn([
                'domain' => $this->di->get('config')->environments->toArray(),
                'strict' => true,
                'allowEmpty' => true,
            ]))
        ;
    }
}
