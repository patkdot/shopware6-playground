<?php

declare(strict_types=1);

namespace KdotPlayground\Service;

use KdotPlayground\Core\Content\Kdot\KdotCollection;
use KdotPlayground\Core\Content\Kdot\KdotDefinition;
use KdotPlayground\Message\KdotUpsertMessage;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Api\Sync\SyncBehavior;
use Shopware\Core\Framework\Api\Sync\SyncOperation;
use Shopware\Core\Framework\Api\Sync\SyncService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;

class KdotService
{
    /**
     * @param EntityRepository<KdotCollection> $kdotRepository
     */
    public function __construct(
        private readonly EntityRepository $kdotRepository,
        private readonly SyncService $syncService,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function upsertFromProductCollectionViaRepository(ProductCollection $productCollection, Context $context, bool $useQueue = true): int
    {
        $upserts = [];
        /** @var ProductEntity $product */
        foreach ($productCollection as $product) {
            $kdotExtension = $product->getExtension('kdot');

            if ($kdotExtension !== null) {
                continue;
            }

            $upserts[] = [
                'id' => Uuid::randomHex(),
                'translations' => [
                    'de-DE' => ['name' => 'Kdot DE ' . $product->getName(), 'description' => 'Kdot DE ' . $product->getDescription()],
                    'en-GB' => ['name' => 'Kdot EN ' . $product->getName(), 'description' => 'Kdot EN ' . $product->getDescription()],
                ],
                'active' => true,
                'productId' => $product->getId(),
            ];
        }

        if (count($upserts) === 0) {
            return 0;
        }

        if ($useQueue) {
            $this->messageBus->dispatch(new KdotUpsertMessage(['data' => $upserts], $context, false));
        } else {
            $this->kdotRepository->upsert($upserts, $context);
        }

        return count($upserts);
    }

    /**
     * @param array<array{id: string, name: string, description: string, kdot_id: string|null}> $products
     */
    public function upsertFromProductCollectionViaSync(array $products, Context $context, bool $useQueue = true): int
    {
        $upserts = [];
        foreach ($products as $product) {
            if ($product['kdot_id'] !== null) {
                continue;
            }
            $upserts[] = [
                'id' => Uuid::randomHex(),
                'translations' => [
                    'de-DE' => ['name' => 'Kdot DE ' . $product['name'], 'description' => 'Kdot DE ' . $product['description']],
                    'en-GB' => ['name' => 'Kdot EN ' . $product['name'], 'description' => 'Kdot EN ' . $product['description']],
                ],
                'active' => true,
                'productId' => $product['id'],
            ];
        }

        if (count($upserts) === 0) {
            return 0;
        }

        if ($useQueue) {
            $this->messageBus->dispatch(new KdotUpsertMessage(['data' => $upserts], $context, true));
        } else {
            $this->syncService->sync([
                new SyncOperation(
                    'write',
                    KdotDefinition::ENTITY_NAME,
                    SyncOperation::ACTION_UPSERT,
                    $upserts
                ),
            ], $context, new SyncBehavior());
        }

        return count($upserts);
    }

    /**
     * @param array<array<string, mixed>> $upserts
     */
    public function directUpsertViaSync(array $upserts, Context $context): int
    {
        $this->syncService->sync([
            new SyncOperation(
                'write',
                KdotDefinition::ENTITY_NAME,
                SyncOperation::ACTION_UPSERT,
                $upserts
            ),
        ], $context, new SyncBehavior());

        return count($upserts);
    }

    /**
     * @param array<array<string, mixed>> $upsert
     */
    public function directUpsertViaRepository(array $upsert, Context $context): int
    {
        $this->kdotRepository->upsert($upsert, $context);

        return count($upsert);
    }
}
