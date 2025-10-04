<?php

declare(strict_types=1);

namespace KdotPlayground\Core\Content\Kdot\SalesChannel;

use KdotPlayground\Core\Content\Kdot\KdotCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

/**
 * @extends StoreApiResponse<EntitySearchResult<KdotCollection>>
 *
 * @property EntitySearchResult<KdotCollection> $object
 */
class KdotRouteResponse extends StoreApiResponse
{
    public function getKdot(): KdotCollection
    {
        /** @var KdotCollection $collection */
        $collection = $this->object->getEntities();

        return $collection;
    }
}
