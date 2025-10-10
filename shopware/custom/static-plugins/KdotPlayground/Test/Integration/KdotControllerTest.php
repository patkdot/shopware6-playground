<?php

declare(strict_types=1);

namespace KdotPlayground\Test\Integration;

use KdotPlayground\Storefront\Controller\KdotController;
use KdotPlayground\Test\TestSetUp;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class KdotControllerTest extends TestSetUp
{

    use IntegrationTestBehaviour;

    private HttpClientInterface $client;
    private KdotController $kdotController;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::getContainer()->get(HttpClientInterface::class);
        $this->kdotController = static::getContainer()->get(KdotController::class);
    }

    public function testshowBoughtProductsHasCustomerWithOrders(): void
    {
        $response = $this->kdotController->showBoughtProducts(
            $this->saleschannelContext,
            $this->orderRepository,
            $this->configService
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseArray = json_decode($response->getContent(), true);
        $this->assertEquals($this->productId, $responseArray[0]['id']);
    }
}
