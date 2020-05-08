<?php
declare(strict_types=1);

namespace Common\BaseMapper;

use Illuminate\Support\Collection;
use Common\Interfaces\ResponseMapperInterface;

class BaseListResponseMapper implements ResponseMapperInterface
{
    protected ResponseMapperInterface $dataResponseMapper;

    public function __construct(ResponseMapperInterface $dataResponseMapper)
    {
        $this->dataResponseMapper = $dataResponseMapper;
    }

    /**
     * @param Collection $object
     * @return array
     */
    public function mapResponseToArray($object): array
    {
        $data = [];
        foreach ($object as $row) {
            $data[] = $this->dataResponseMapper->mapResponseToArray($row);
        }

        return $data;
    }
}
