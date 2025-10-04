<?php

declare(strict_types=1);

namespace KdotPlayground\Service;

use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Doctrine\DBAL\Connection;
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
        return $this->productRepository->search($criteria, Context::createDefaultContext())->getEntities();
    }

    public function getAllProductsViaRawSql(Context $context): array
    {
        $sql = $this->connection->createQueryBuilder();
        $sql
            ->select('LOWER(HEX(p.id)) as id, pt.name, pt.description, hex(k.id) as kdot_id')
            ->from('product', 'p')
            ->leftJoin('p', 'kdot',  'k', 'p.id = k.product_id')
            ->join('p', 'product_translation', 'pt', 'p.id = pt.product_id')
            ->where('pt.language_id = :languageId')
            ->setParameter('languageId', Uuid::fromHexToBytes($context->getLanguageId()));

        return $sql->executeQuery()->fetchAllAssociative();
    }
}
