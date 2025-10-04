<?php

declare(strict_types=1);

namespace KdotPlayground\Core\Content\Kdot\SalesChannel;

use KdotPlayground\Core\Content\Kdot\KdotCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Routing\StoreApiRouteScope;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [StoreApiRouteScope::ID]])]
class KdotRoute extends AbstractKdotRoute
{
    /**
     * @param EntityRepository<KdotCollection> $kdotRepository
     */
    public function __construct(private readonly EntityRepository $kdotRepository)
    {
    }

    public function getDecorated(): AbstractKdotRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(
        path: '/store-api/kdot',
        name: 'store-api.kdot.search',
        defaults: ['_entity' => 'kdot', 'fields' => ['id', 'translated' => ['name', 'description'], 'active']],
        methods: ['GET', 'POST']
    )]
    public function load(Criteria $criteria, SalesChannelContext $context): KdotRouteResponse
    {
        $result = $this->kdotRepository->search($criteria, $context->getContext());

        return new KdotRouteResponse($result);
    }
}
