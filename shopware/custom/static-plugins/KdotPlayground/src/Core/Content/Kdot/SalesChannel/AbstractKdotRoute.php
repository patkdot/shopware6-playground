<?php declare(strict_types=1);

namespace KdotPlayground\Core\Content\Kdot\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

abstract class AbstractKdotRoute
{
    abstract public function getDecorated(): AbstractKdotRoute;

    abstract public function load(Criteria $criteria, SalesChannelContext $context): KdotRouteResponse;
}
