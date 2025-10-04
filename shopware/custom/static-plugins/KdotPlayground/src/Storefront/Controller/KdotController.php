<?php

declare(strict_types=1);

namespace KdotPlayground\Storefront\Controller;

use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Order\OrderStates;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Routing\StorefrontRouteScope;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [StorefrontRouteScope::ID]])]
class KdotController extends StorefrontController
{
    #[Route(
        path: '/kdot',
        name: 'frontend.kdot.index',
        methods: ['GET']
    )]
    public function showIndex(Request $request, SalesChannelContext $context): Response
    {
        return $this->renderStorefront('@KdotPlayground/storefront/page/index.html.twig', [
            'kdot' => 'Hello world',
        ]);
    }

    /**
     * @param EntityRepository<OrderCollection> $orderRepository
     */
    #[Route(
        path: '/kdot/products',
        name: 'frontend.kdot.products',
        methods: ['GET'],
        defaults: ['XmlHttpRequest' => true]
    )]
    public function showBoughtProducts(Request $request, SalesChannelContext $context, EntityRepository $orderRepository, SystemConfigService $config): Response
    {
        if ($config->get('KdotPlayground.config.active') === false) {
            return $this->json(['error' => 'activate the plugin first']);
        }

        if (!$context->getCustomerId()) {
            return $this->json([]);
        }

        $criteria = new Criteria();
        $criteria->addAssociation('lineItems.product.kdot');
        $criteria->addAssociation('orderCustomer');
        $criteria->addFilter(new EqualsFilter('orderCustomer.customerId', $context->getCustomerId()));
        $criteria->addFilter(
            new NotFilter(
                NotFilter::CONNECTION_OR,
                [
                    new EqualsFilter('order.stateMachineState.technicalName', OrderStates::STATE_CANCELLED),
                ]
            )
        );

        $orders = $orderRepository->search($criteria, $context->getContext())->getEntities();

        $products = [];

        foreach ($orders as $order) {
            $lineItems = $order->getLineItems();

            if ($lineItems === null) {
                continue;
            }

            foreach ($lineItems as $lineItem) {
                $product = $lineItem->getProduct();

                if ($product !== null) {
                    $products[] = $product;
                }
            }
        }

        return $this->json($products);
    }
}
