<?php

declare(strict_types=1);

namespace KdotPlayground\Service;

use KdotPlayground\Core\Content\Kdot\KdotCollection;
use KdotPlayground\Core\Content\Kdot\KdotDefinition;
use KdotPlayground\Flow\Event\KdotEvent;
use KdotPlayground\Message\KdotUpsertMessage;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Api\Sync\SyncBehavior;
use Shopware\Core\Framework\Api\Sync\SyncOperation;
use Shopware\Core\Framework\Api\Sync\SyncService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    public function upsertFromProductCollectionViaRepository(ProductCollection $productCollection, Context $context, bool $useQueue = true): int
    {
        $upserts = [];
        /** @var ProductEntity $product */
        foreach ($productCollection as $product) {
            $kdotCollection = $product->getExtension('kdot');

            if ($kdotCollection instanceof KdotCollection && $kdotCollection->count() > 0) {
                continue;
            }

            $upserts[] = [
                'id' => Uuid::randomHex(),
                'translations' => [
                    'de-DE' => ['name' => 'Kdot DE name', 'description' => 'Kdot DE description'],
                    'en-GB' => ['name' => 'Kdot EN name', 'description' => 'Kdot EN description'],
                ],
                'active' => true,
                'productId' => $product->getId(),
            ];
        }

        if (count($upserts) === 0) {
            return 0;
        }

        if ($useQueue) {
            $this->messageBus->dispatch(new KdotUpsertMessage($upserts, $context, false));
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
                    'de-DE' => ['name' => 'Kdot DE name', 'description' => 'Kdot DE description'],
                    'en-GB' => ['name' => 'Kdot EN name', 'description' => 'Kdot EN description'],
                ],
                'active' => true,
                'productId' => $product['id'],
            ];
        }

        if (count($upserts) === 0) {
            return 0;
        }

        if ($useQueue) {
            $this->messageBus->dispatch(new KdotUpsertMessage($upserts, $context, true));
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
        $this->dispatcher->dispatch(new KdotEvent($upserts, $context));

        return count($upserts);
    }

    /**
     * @param array<array<string, mixed>> $upsert
     */
    public function directUpsertViaRepository(array $upsert, Context $context): int
    {
        $this->kdotRepository->upsert($upsert, $context);
        $this->dispatcher->dispatch(new KdotEvent($upsert, $context));

        return count($upsert);
    }
}
