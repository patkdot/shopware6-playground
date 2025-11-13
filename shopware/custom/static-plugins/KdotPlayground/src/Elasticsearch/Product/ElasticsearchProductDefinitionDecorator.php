<?php declare(strict_types=1);

namespace KdotPlayground\Elasticsearch\Product;

use Doctrine\DBAL\Connection;
use OpenSearchDSL\Query\Compound\BoolQuery;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Elasticsearch\Framework\AbstractElasticsearchDefinition;
use Doctrine\DBAL\ArrayParameterType;

class ElasticsearchProductDefinitionDecorator extends AbstractElasticsearchDefinition
{
    public function __construct(
        private readonly AbstractElasticsearchDefinition $decorated,
        private readonly Connection $connection
    ) {
    }

    public function getEntityDefinition(): EntityDefinition
    {
        return $this->decorated->getEntityDefinition();
    }

    public function buildTermQuery(Context $context, Criteria $criteria): BoolQuery
    {
        return $this->decorated->buildTermQuery($context, $criteria);
    }

    public function getMapping(Context $context): array
    {
        $mappings = $this->decorated->getMapping($context);

        $additionalMappings = [
            'kdot' => [
                'type' => 'nested',
                'properties' => [
                    'active' => AbstractElasticsearchDefinition::BOOLEAN_FIELD,
                ]
            ],
        ];

        $mappings['properties'] = array_merge($mappings['properties'], $additionalMappings);

        return $mappings;
    }

    public function fetch(array $ids, Context $context): array
    {
        $data = $this->decorated->fetch($ids, $context);

        $documents = [];
        $kdots = $this->fetchKdots($ids);

        foreach ($data as $id => $document) {
            if (isset($kdots[$document['id']])) {
                $document['kdot'] = $kdots[$document['id']];
            }

            $documents[$id] = $document;
        }

        return $documents;
    }

    private function fetchKdots(array $ids): array
    {
        $query = <<<SQL
            SELECT LOWER(HEX(product_id)) as id, active
            FROM kdot
            WHERE
                product_id IN (:ids)
        SQL;

        $dbKdots = $this->connection->fetchAllAssociative(
            $query,
            [
                'ids' => $ids,
            ],
            [
                'ids' => ArrayParameterType::STRING
            ]
        );

        $kdots = [];

        foreach ($dbKdots as $kdot) {
            $kdots[$kdot['id']]['active'] = (bool) $kdot['active'];
        }

        return $kdots;
    }
}