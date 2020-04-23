<?php
declare(strict_types=1);

namespace Common\BaseMapper;

use Common\Interfaces\ResponseMapperInterface;
use Documentation\Entity\ResponseDoc;
use Illuminate\Support\Collection;

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

    public function responseDocumentation(): ?ResponseDoc
    {
        // TODO: write response documentation, create default paginated result doc
        return null;
    }
}
