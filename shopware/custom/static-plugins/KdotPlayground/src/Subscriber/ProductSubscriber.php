<?php

declare(strict_types=1);

namespace KdotPlayground\Subscriber;

use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSearchCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSuggestCriteriaEvent;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Shopware\Storefront\Page\Product\ProductPageCriteriaEvent;

class ProductSubscriber
{
    public function __construct(
        private readonly SystemConfigService $config
    ) {}

    #[AsEventListener(event: ProductPageLoadedEvent::class)]
    public function onProductsLoaded(ProductPageLoadedEvent $event): void
    {
        if ($this->config->get('KdotPlayground.config.active') === false) {
            return;
        }

        $event->getPage()->setExtensions(['kdot' => new ArrayStruct(['kdot' => 'test'])]);
    }

    #[AsEventListener(event: ProductEvents::PRODUCT_LISTING_CRITERIA)]
    #[AsEventListener(event: ProductEvents::PRODUCT_SUGGEST_CRITERIA)]
    #[AsEventListener(event: ProductEvents::PRODUCT_SEARCH_CRITERIA)]
    #[AsEventListener(event: ProductPageCriteriaEvent::class)]
    public function onProductListingCriteria(ProductListingCriteriaEvent|ProductSearchCriteriaEvent|ProductSuggestCriteriaEvent|ProductPageCriteriaEvent $event): void
    {
        if ($this->config->get('KdotPlayground.config.active') === false) {
            return;
        }

        $event->getCriteria()->addAssociation('kdot');
    }
}
