<?php
declare(strict_types=1);

namespace Example\Config;

use Carbon\Carbon;
use Common\BaseClasses\BaseRoutes;
use Common\Entity\TestEntity;

class Routes extends BaseRoutes
{
    public function get()
    {
        $array = [
            'integer' => 1,
            'integer_nullable' => 2,
            'float' => 1.00,
            'float_nullable' => 2.00,
            'string' => 'test',
            'string_nullable' => null,
            'boolean' => false,
            'boolean_nullable' => true,
            'array' => ['t' => 123, 't2' => 'tt'],
            'entity' => [
                'name' => 'test',
                'path' => '/122/test',
                'map' => ['123', 'test'],
            ],
//            'entity' => (new DirectoryEntity())
//                ->setName('test')
//                ->setPath('/122/test')
//                ->setMap(['123', 'test']),
            'object' => Carbon::now()->timestamp,
        ];

//        $entity = (new TestEntity())
//            ->setInteger($array['integer'])
//            ->setIntegerNullable($array['integerNullable'])
//            ->setFloat($array['float'])
//            ->setFloatNullable($array['floatNullable'])
//            ->setString($array['string'])
//            ->setStringNullable($array['stringNullable'])
//            ->setBoolean($array['boolean'])
//            ->setBooleanNullable(null)
//            ->setArray($array['array'])
//            ->setEntity($array['entity'])
//            ->setObject($array['object'])
//        ;

        dd((new TestEntity())->toEntity($array));

        return [
            'test' => 'ok',
        ];
    }
}
