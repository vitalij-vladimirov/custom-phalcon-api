<?php
declare(strict_types=1);

namespace Example\Mapper;

use Common\Interfaces\RequestMapperInterface;
use Common\Interfaces\ResponseMapperInterface;
use Example\Model\VendorModel;

class VendorMapper implements RequestMapperInterface, ResponseMapperInterface
{
    public function mapRequestToObject(array $data): VendorModel
    {
        return (new VendorModel())
            ->setLibName($data['lib_name'])
            ->setLibUrl($data['lib_url'])
            ->setVersion($data['version'])
            ->setEnvironment($data['environment'])
            ->setDescription($data['description'])
        ;
    }

    /**
     * @param VendorModel $object
     * @return array
     */
    public function mapResponseToArray($object): array
    {
        $object->setHidden(['created_at', 'updated_at']);

        return $object->toArray();
    }
}
