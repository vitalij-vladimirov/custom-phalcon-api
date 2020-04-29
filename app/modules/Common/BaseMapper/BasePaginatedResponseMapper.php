<?php
declare(strict_types=1);

namespace Common\BaseMapper;

use Common\Entity\PaginatedResult;
use Common\Interfaces\ResponseMapperInterface;
use Documentation\Entity\ResponseDoc;

class BasePaginatedResponseMapper implements ResponseMapperInterface
{
    protected ResponseMapperInterface $dataResponseMapper;

    public function __construct(ResponseMapperInterface $dataResponseMapper)
    {
        $this->dataResponseMapper = $dataResponseMapper;
    }

    /**
     * @param PaginatedResult $object
     * @return array
     */
    public function mapResponseToArray($object): array
    {
        return $this->getPaginatedResponse($object);
    }

    public function responseDocumentation(): ?ResponseDoc
    {
        // TODO: write response documentation, create default paginated result doc
        return null;
    }

    private function getPaginatedResponse(PaginatedResult $paginatedResult): array
    {
        $data = [];
        foreach ($paginatedResult->getData() as $row) {
            $data[] = $this->dataResponseMapper->mapResponseToArray($row);
        }

        return [
            'total_results' => $paginatedResult->getTotalResults(),
            'total_pages' => $paginatedResult->getTotalPages(),
            'current_page' => $paginatedResult->getCurrentPage(),
            'limit' => $paginatedResult->getLimit(),
            'data' => $data
        ];
    }
}
