<?php

declare(strict_types=1);

namespace KdotPlayground\Core\Content\Kdot\SalesChannel;

use KdotPlayground\Core\Content\Kdot\KdotCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

/**
 * @property EntitySearchResult $object
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
