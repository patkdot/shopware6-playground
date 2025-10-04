<?php

declare(strict_types=1);

namespace KdotPlayground\Service;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class ProductService
{
    /**
     * @param EntityRepository<ProductCollection> $productRepository
     */
    public function __construct(
        private readonly EntityRepository $productRepository,
        private readonly Connection $connection,
    ) {
    }

    public function getAllProducts(): ProductCollection
    {
        $criteria = new Criteria();
        $criteria->addAssociation('kdot');
        $criteria->addFilter(
            new NotFilter(
                NotFilter::CONNECTION_OR,
                [
                    new EqualsFilter('name', null),
                    new EqualsFilter('name', ''),
                ]
            )
        );

        return $this->productRepository->search($criteria, Context::createDefaultContext())->getEntities();
    }

    /**
     * @return array<array{id: string, name: string, description: string, kdot_id: string|null}>
     */
    public function getAllProductsViaRawSql(Context $context): array
    {
        $sql = $this->connection->createQueryBuilder();
        $sql
            ->select('LOWER(HEX(p.id)) as id, pt.name, pt.description, hex(k.id) as kdot_id')
            ->from('product', 'p')
            ->leftJoin('p', 'kdot', 'k', 'p.id = k.product_id')
            ->join('p', 'product_translation', 'pt', 'p.id = pt.product_id')
            ->where('pt.language_id = :languageId')
            ->setParameter('languageId', Uuid::fromHexToBytes($context->getLanguageId()));

        $result = $sql->executeQuery()->fetchAllAssociative();

        // Ensure consistent return type
        return array_map(function (array $row) {
            return [
                'id' => (string)($row['id'] ?? ''),
                'name' => (string)($row['name'] ?? ''),
                'description' => (string)($row['description'] ?? ''),
                'kdot_id' => $row['kdot_id'] !== null ? (string)$row['kdot_id'] : null,
            ];
        }, $result);
    }
}
