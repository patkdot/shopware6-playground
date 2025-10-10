<?php

declare(strict_types=1);

namespace KdotPlayground\Test;

use KdotPlayground\Service\KdotService;
use KdotPlayground\Service\ProductService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;

abstract class TestSetUp extends TestCase
{
    use IntegrationTestBehaviour;

    protected EntityRepository $productRepository;
    protected EntityRepository $customerRepository;
    protected EntityRepository $addressRepository;
    protected EntityRepository $orderRepository;
    protected EntityRepository $orderLineItemRepository;
    protected EntityRepository $orderCustomerRepository;
    protected string $customerId;
    protected ProductService $productService;
    protected KdotService $kdotService;
    protected EntityRepository $kdotRepository;
    protected EntityRepository $saleschannelRepository;
    protected EntityRepository $customerGroupRepository;
    protected SalesChannelEntity $saleschannel;
    protected CustomerEntity $customer;
    protected SalesChannelContext $saleschannelContext;
    protected string $productId;
    protected SystemConfigService $configService;

    public function setUp(): void
    {
        static::getKernel()::getConnection()->beginTransaction();
        static::getKernel()::getConnection()->executeStatement('DELETE FROM product WHERE 1');
        static::getKernel()::getConnection()->executeStatement('DELETE FROM customer WHERE 1');
        static::getKernel()::getConnection()->executeStatement('DELETE FROM customer_address WHERE 1');
        static::getKernel()::getConnection()->executeStatement('DELETE FROM `order` WHERE 1');

        $this->productRepository = static::getContainer()->get('product.repository');
        $this->customerRepository = static::getContainer()->get('customer.repository');
        $this->addressRepository = static::getContainer()->get('customer_address.repository');
        $this->orderRepository = static::getContainer()->get('order.repository');
        $this->orderLineItemRepository = static::getContainer()->get('order_line_item.repository');
        $this->orderCustomerRepository = static::getContainer()->get('order_customer.repository');
        $this->kdotRepository = static::getContainer()->get('kdot.repository');
        $this->saleschannelRepository = static::getContainer()->get('sales_channel.repository');
        $this->customerGroupRepository = static::getContainer()->get('customer_group.repository');
        $this->productService = static::getContainer()->get('KdotPlayground\Service\ProductService');
        $this->kdotService = static::getContainer()->get('KdotPlayground\Service\KdotService');
        $this->configService = static::getContainer()->get('Shopware\Core\System\SystemConfig\SystemConfigService');

        $context = Context::createDefaultContext();
        $this->saleschannel = $this->saleschannelRepository->search((new Criteria())->addAssociation('domains'), $context)->first();
        /** @var saleschannelContextFactory SalesChannelContextFactory */
        $saleschannelContextFactory = static::getContainer()->get(SalesChannelContextFactory::class);

        $this->customerId = Uuid::randomHex();

        $addressId = Uuid::randomHex();
        $this->productId = Uuid::randomHex();
        $currencyId = Defaults::CURRENCY;
        $taxId = $this->getValidTaxId();
        $salesChannelId = $this->saleschannel?->getId();
        $defaultPaymentId = $this->getValidPaymentMethodId();
        $countryId = $this->getDeCountryId();
        $stateId = $this->getStateMachineState();
        $orderId = Uuid::randomHex();
        $kdotId = Uuid::randomHex();
        $groupId = $this->customerGroupRepository->searchIds(new Criteria(), $context)->firstId();

        $this->productRepository->create([
            [
                'id' => $this->productId,
                'name' => 'test',
                'productNumber' => 'random',
                'stock' => 10,
                'taxId' => $taxId,
                'active' => true,
                'price' => [
                    [
                        'currencyId' => Defaults::CURRENCY,
                        'gross' => 15,
                        'net' => 10,
                        'linked' => false,
                    ],
                ],
                'kdot' => [[
                    'id' => $kdotId,
                    'productId' => $this->productId,
                    'translations' => [
                        'de-DE' => ['name' => 'Kdot DE'],
                        'en-GB' => ['name' => 'Kdot EN'],
                    ],
                    'active' => true,
                ]],
            ],
        ], $context);

        $this->customerRepository->create([
            [
                'id' => $this->customerId,
                'email' => 'pat.kdot@example.com',
                'firstName' => 'pat',
                'lastName' => 'kdot',
                'customerNumber' => '123456',
                'salesChannelId' => $salesChannelId,
                'groupId' => $groupId,
                'defaultPaymentMethodId' => $defaultPaymentId,
                'defaultBillingAddress' => [
                    'id' => $addressId,
                    'customerId' => $this->customerId,
                    'street' => 'street 1',
                    'zipcode' => '12345',
                    'city' => 'ddorf',
                    'countryId' => $countryId,
                    'firstName' => 'pat',
                    'lastName' => 'kdot',
                ],
                'defaultShippingAddress' => [
                    'id' => $addressId,
                    'customerId' => $this->customerId,
                    'street' => 'street 1',
                    'zipcode' => '12345',
                    'city' => 'ddorf',
                    'countryId' => $countryId,
                    'firstName' => 'pat',
                    'lastName' => 'kdot',
                ],
            ],
        ], $context);

        $this->orderRepository->create([
            [
                'id' => $orderId,
                'shippingCosts' => [
                    'unitPrice' => 10,
                    'totalPrice' => 10,
                    'quantity' => 1,
                    'calculatedTaxes' => [
                        [
                            'tax' => 10,
                            'taxRate' => 19,
                            'price' => 10,
                        ],
                    ],
                    'taxRules' => [
                        [
                            'taxRate' => 19,
                            'percentage' => 100,
                        ],
                    ],
                ],
                'orderNumber' => '10001',
                'orderDateTime' => new \DateTime(),
                'stateId' => $stateId,
                'customerId' => $this->customerId,
                'orderLineItems' => [
                    [
                        'productId' => $this->productId,
                        'quantity' => 1,
                    ],
                ],
                'billingAddressId' => $addressId,
                'currencyId' => $currencyId,
                'salesChannelId' => $salesChannelId,
                'currencyFactor' => 1,
                'itemRounding' => [
                    'decimals' => 2,
                    'interval' => 0.01,
                    'roundForNet' => false,
                ],
                'totalRounding' => [
                    'decimals' => 2,
                    'interval' => 0.01,
                    'roundForNet' => false,
                ],
                'price' => [
                    'totalPrice' => 10,
                    'netPrice' => 10,
                    'positionPrice' => 10,
                    'rawTotal' => 10,
                    'taxStatus' => 'gross',
                    'taxRules' => [
                        [
                            'taxRate' => 19,
                            'percentage' => 100,
                        ],
                    ],
                    'calculatedTaxes' => [
                        [
                            'tax' => 10,
                            'taxRate' => 19,
                            'price' => 10,
                        ],
                    ],
                ],
            ],
        ], $context);

        $this->orderLineItemRepository->create([
            [
                'orderId' => $orderId,
                'productId' => $this->productId,
                'identifier' => $this->productId,
                'quantity' => 1,
                'price' => [
                    'unitPrice' => 10,
                    'totalPrice' => 10,
                    'quantity' => 1,
                    'calculatedTaxes' => [
                        [
                            'tax' => 10,
                            'taxRate' => 19,
                            'price' => 10,
                        ],
                    ],
                    'taxRules' => [
                        [
                            'taxRate' => 19,
                            'percentage' => 100,
                        ],
                    ],
                ],
                'label' => 'test',
                'payload' => [
                    'productNumber' => 'random',
                ],
            ],
        ], $context);

        $this->orderCustomerRepository->create([
            [
                'orderId' => $orderId,
                'customerId' => $this->customerId,
                'email' => 'pat.kdot@example.com',
                'firstName' => 'pat',
                'lastName' => 'kdot',
            ],
        ], $context);

        static::getKernel()::getConnection()->commit();

        $this->customer = $this->customerRepository->search(new Criteria([$this->customerId]), $context)->first();

        // $this->saleschannelContext = $saleschannelContextFactory->create(Uuid::randomHex(), $this->saleschannel->getId(), ['customer' => $this->customer]);
        $this->saleschannelContext = $saleschannelContextFactory->create(Uuid::randomHex(), $this->saleschannel->getId(), [
            SalesChannelContextService::CUSTOMER_ID => $this->customer->getId(),
        ]);
    }
}
