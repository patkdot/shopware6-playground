<?php

declare(strict_types=1);

namespace KdotPlayground\Subscriber;

use KdotPlayground\Core\Content\Kdot\KdotCollection;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSearchCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSuggestCriteriaEvent;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ProductSubscriber
{
    /**
     * @param EntityRepository<KdotCollection> $kdotRepository
     */
    public function __construct(
        private readonly EntityRepository $kdotRepository,
        private readonly SystemConfigService $config
    ) {
    }

    #[AsEventListener(event: ProductPageLoadedEvent::class)]
    public function onProductsLoaded(ProductPageLoadedEvent $event): void
    {
        if ($this->config->get('KdotPlayground.config.active') === false) {
            return;
        }

        $product = $event->getPage()->getProduct();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $product->getId()));
        $kdot = $this->kdotRepository->search($criteria, $event->getSalesChannelContext()->getContext())->getEntities()->first();

        if ($kdot === null) {
            return;
        }
        $event->getPage()->setExtensions(['kdot' => $kdot]);
    }

    #[AsEventListener(event: ProductEvents::PRODUCT_LISTING_CRITERIA)]
    #[AsEventListener(event: ProductEvents::PRODUCT_SUGGEST_CRITERIA)]
    #[AsEventListener(event: ProductEvents::PRODUCT_SEARCH_CRITERIA)]
    public function onProductListingCriteria(ProductListingCriteriaEvent|ProductSearchCriteriaEvent|ProductSuggestCriteriaEvent $event): void
    {
        $event->getCriteria()->addAssociation('kdot');
    }
}
